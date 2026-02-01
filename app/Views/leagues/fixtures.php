<?php
// Sort fixtures by date desc, then time
usort($fixtures, function ($a, $b) {
    if ($a['date'] === $b['date']) {
        return strcmp($a['time'], $b['time']);
    }
    return strcmp($a['date'], $b['date']);
});
?>

<div class="text-center mb-12">
    <h1
        class="text-4xl font-extrabold tracking-tight mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-slate-400">
        <?= htmlspecialchars($league['name']) ?>
    </h1>
    <h2 class="text-2xl font-bold mb-6 text-text-muted">Fixtures</h2>
</div>

<div class="card">
    <div class="flex justify-end gap-4 mb-8">
        <a href="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>" class="btn btn-secondary">
            Back to League
        </a>
    </div>

    <?php
    $modalId = 'regenerateModal';
    $competitionType = 'league';
    $formAction = $basePath . '/admin/leagues/' . htmlspecialchars($league['slug'] ?? $league['id']) . '/regenerate-fixtures';
    $competition = $league;
    include __DIR__ . '/../partials/regenerate_modal.php';
    ?>

    <?php if (empty($fixtures)): ?>
        <div class="text-center p-12">
            <p class="text-text-muted">No fixtures scheduled yet.</p>
        </div>
    <?php else: ?>
        <?php
        $groupedFixtures = [];
        foreach ($fixtures as $fixture) {
            $date = $fixture['date'];
            if (!isset($groupedFixtures[$date])) {
                $groupedFixtures[$date] = [];
            }
            $groupedFixtures[$date][] = $fixture;
        }
        ?>

        <?php foreach ($groupedFixtures as $date => $dateFixtures): ?>
            <div class="mb-12">
                <h3 class="bg-surface-hover py-3 px-6 -mx-8 mb-6 border-y border-border text-lg text-primary font-bold">
                    <?= date('l, j F Y', strtotime($date)) ?>
                </h3>

                <div class="grid gap-4">
                    <?php foreach ($dateFixtures as $fixture): ?>
                        <div class="bg-background border border-border rounded-sm p-6">
                            <form method="POST"
                                action="<?=$basePath?>/admin/leagues/<?= htmlspecialchars($league['slug'] ?? $league['id']) ?>/fixtures">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="fixtureId" value="<?= htmlspecialchars($fixture['id']) ?>">

                                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-8 mb-4">
                                    <!-- Home Team -->
                                    <div class="text-right flex items-center justify-end gap-4">
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['homeTeamName']) ?></strong>
                                        <span class="inline-block w-4 h-4 rounded bg-current"
                                            style="color: <?= htmlspecialchars($fixture['homeTeamColour'] ?? '#000') ?>;"></span>
                                    </div>

                                    <!-- Scores -->
                                    <div class="flex items-center gap-4 bg-surface px-4 py-2 rounded-sm border border-border">
                                        <input type="number" name="homeScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['homeScore']) : '' ?>"
                                            placeholder="-">
                                        <span class="text-text-muted font-bold">vs</span>
                                        <input type="number" name="awayScore" min="0" max="99"
                                            class="w-12 text-center p-1 text-xl font-bold text-primary bg-transparent border-none focus:ring-0 appearance-none"
                                            value="<?= $fixture['result'] !== null ? htmlspecialchars((string) $fixture['result']['awayScore']) : '' ?>"
                                            placeholder="-">
                                    </div>

                                    <!-- Away Team -->
                                    <div class="text-left flex items-center justify-start gap-4">
                                        <span class="inline-block w-4 h-4 rounded bg-current"
                                            style="color: <?= htmlspecialchars($fixture['awayTeamColour'] ?? '#000') ?>;"></span>
                                        <strong class="text-lg"><?= htmlspecialchars($fixture['awayTeamName']) ?></strong>
                                    </div>
                                </div>

                                <div class="flex justify-between items-center border-t border-border pt-4">
                                    <div class="flex gap-4 text-text-muted text-sm">
                                        <span>Time: <input type="time" name="time" value="<?= htmlspecialchars($fixture['time']) ?>"
                                                class="bg-transparent border border-border text-text-main p-1 rounded w-auto focus:border-primary focus:outline-none"></span>
                                        <span>Date: <input type="date" name="date" value="<?= htmlspecialchars($fixture['date']) ?>"
                                                class="bg-transparent border border-border text-text-main p-1 rounded w-auto focus:border-primary focus:outline-none"></span>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </div>

                                <details class="mt-4">
                                    <summary class="cursor-pointer text-text-muted text-sm">Match Details (Scorers & Cards)
                                    </summary>
                                    <div class="grid grid-cols-2 gap-8 mt-4 bg-surface p-6 rounded-sm">
                                        <div>
                                            <label
                                                class="block mb-2 text-xs uppercase tracking-wide font-semibold text-text-muted">Home
                                                Details</label>
                                            <input type="text" name="homeScorers" placeholder="Scorers (e.g. Smith 23')"
                                                value="<?= htmlspecialchars($fixture['result']['homeScorers'] ?? '') ?>"
                                                class="form-input mb-2 text-sm py-2">
                                            <input type="text" name="homeCards" placeholder="Cards (e.g. Wilson (Y))"
                                                value="<?= htmlspecialchars($fixture['result']['homeCards'] ?? '') ?>"
                                                class="form-input text-sm py-2">
                                        </div>
                                        <div>
                                            <label
                                                class="block mb-2 text-xs uppercase tracking-wide font-semibold text-text-muted">Away
                                                Details</label>
                                            <input type="text" name="awayScorers" placeholder="Scorers (e.g. Brown 45')"
                                                value="<?= htmlspecialchars($fixture['result']['awayScorers'] ?? '') ?>"
                                                class="form-input mb-2 text-sm py-2">
                                            <input type="text" name="awayCards" placeholder="Cards (e.g. Taylor (R))"
                                                value="<?= htmlspecialchars($fixture['result']['awayCards'] ?? '') ?>"
                                                class="form-input text-sm py-2">
                                        </div>
                                    </div>
                                </details>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="mt-8 pt-6 border-t border-border">
        <button type="button"
            onclick="document.getElementById('regenerateModal').classList.remove('hidden'); document.getElementById('regenerateModal').classList.add('flex');"
            class="btn bg-orange-500 text-black hover:bg-orange-600 font-bold border-none w-full sm:w-auto">
            Regenerate Fixtures
        </button>
    </div>
</div>

