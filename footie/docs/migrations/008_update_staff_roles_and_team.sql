-- Migration 008: Update team_staff to support multiple roles and optional team assignment

-- 1. Make team_id nullable
ALTER TABLE `team_staff` MODIFY `team_id` int(11) DEFAULT NULL;

-- 2. Change role from enum to varchar to support comma-separated roles
ALTER TABLE `team_staff` MODIFY `role` varchar(255) NOT NULL;
