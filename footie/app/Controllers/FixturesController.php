<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Controller;

/**
 * FixturesController - Admin fixture detail management.
 * Handles both league and cup fixtures.
 */
class FixturesController extends Controller
{
    /**
     * Display admin fixture detail editor.
     */
    public function fixtureDetail(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        // Validate fixture type
        if (!in_array($type, ['league', 'cup'])) {
            $this->redirect('/admin');
            return;
        }

        // Parse fixture slug (format: "home-team-slug-vs-away-team-slug")
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->redirect('/admin');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];

        // Load competition and find fixture
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->redirect('/admin/leagues');
                return;
            }

            $fixture = $this->findFixtureByTeamSlugs(
                $competition['fixtures'],
                $homeTeamSlug,
                $awayTeamSlug
            );

            if ($fixture) {
                $fixtureDetail = $leagueModel->getFixtureById($fixture['id']);
            }
        } else {
            $cupModel = new \App\Models\Cup();
            $competition = $cupModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->redirect('/admin/cups');
                return;
            }

            $fixture = $this->findFixtureInRounds(
                $competition['rounds'],
                $homeTeamSlug,
                $awayTeamSlug
            );

            if ($fixture) {
                $fixtureDetail = $cupModel->getFixtureById($fixture['id']);
            }
        }

        if (!isset($fixtureDetail) || !$fixtureDetail) {
            $this->redirect('/admin');
            return;
        }

        $this->render('fixtures/detail', [
            'title' => 'Edit Fixture: ' . $fixtureDetail['homeTeamName'] . ' vs ' . $fixtureDetail['awayTeamName'],
            'fixtureType' => $type,
            'competition' => $competition,
            'fixture' => $fixtureDetail,
            'fixtureSlug' => $fixtureSlug,
        ]);
    }

    /**
     * Update fixture rich content details.
     */
    public function updateFixtureDetail(string $type, string $competitionSlug, string $fixtureSlug): void
    {
        // Validate fixture type
        if (!in_array($type, ['league', 'cup'])) {
            $this->flash('error', 'Invalid fixture type');
            $this->redirect('/admin');
            return;
        }

        // Parse fixture slug
        if (!preg_match('/^(.+)-vs-(.+)$/', $fixtureSlug, $matches)) {
            $this->flash('error', 'Invalid fixture');
            $this->redirect('/admin');
            return;
        }

        $homeTeamSlug = $matches[1];
        $awayTeamSlug = $matches[2];

        // Load competition and find fixture
        if ($type === 'league') {
            $leagueModel = new \App\Models\League();
            $competition = $leagueModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/leagues');
                return;
            }

            $fixture = $this->findFixtureByTeamSlugs(
                $competition['fixtures'],
                $homeTeamSlug,
                $awayTeamSlug
            );
        } else {
            $cupModel = new \App\Models\Cup();
            $competition = $cupModel->findWhere('slug', $competitionSlug);

            if (!$competition) {
                $this->flash('error', 'Competition not found');
                $this->redirect('/admin/cups');
                return;
            }

            $fixture = $this->findFixtureInRounds(
                $competition['rounds'],
                $homeTeamSlug,
                $awayTeamSlug
            );
        }

        if (!$fixture) {
            $this->flash('error', 'Fixture not found');
            $this->redirect('/admin');
            return;
        }

        // Get form data
        $details = [
            'status' => $this->post('status'),
            'matchReport' => $this->post('match_report'),
            'liveStreamUrl' => $this->post('live_stream_url'),
            'fullMatchUrl' => $this->post('full_match_url'),
            'highlightsUrl' => $this->post('highlights_url'),
        ];

        // Update fixture details
        if ($type === 'league') {
            $leagueModel->updateFixtureDetails($fixture['id'], $details);
        } else {
            $cupModel->updateFixtureDetails($fixture['id'], $details);
        }

        $this->flash('success', 'Fixture details updated successfully');
        $this->redirect("/admin/fixture/{$type}/{$competitionSlug}/{$fixtureSlug}");
    }

    /**
     * Find fixture by team slugs in a fixture list.
     */
    private function findFixtureByTeamSlugs(array $fixtures, string $homeSlug, string $awaySlug): ?array
    {
        foreach ($fixtures as $fixture) {
            if (isset($fixture['homeTeam']['slug'], $fixture['awayTeam']['slug'])) {
                if ($fixture['homeTeam']['slug'] === $homeSlug &&
                    $fixture['awayTeam']['slug'] === $awaySlug) {
                    return $fixture;
                }
            }
        }
        return null;
    }

    /**
     * Find fixture by team slugs in cup rounds.
     */
    private function findFixtureInRounds(array $rounds, string $homeSlug, string $awaySlug): ?array
    {
        foreach ($rounds as $round) {
            foreach ($round['fixtures'] as $fixture) {
                if (isset($fixture['homeTeam']['slug'], $fixture['awayTeam']['slug'])) {
                    if ($fixture['homeTeam']['slug'] === $homeSlug &&
                        $fixture['awayTeam']['slug'] === $awaySlug) {
                        return $fixture;
                    }
                }
            }
        }
        return null;
    }
}
