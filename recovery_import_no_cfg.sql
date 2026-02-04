-- =====================================================
-- OPTICREW DATABASE RECOVERY - IMPORT WITHOUT .CFG FILES
-- =====================================================
-- This script handles importing .ibd files when .cfg files are missing
-- Run this AFTER you have:
-- 1. Created all tables
-- 2. Run DISCARD TABLESPACE on all tables
-- 3. Copied .ibd files back to C:\xampp\mysql\data\opticrew
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. USERS TABLE
-- =====================================================
-- When importing without .cfg file, ALL secondary indexes must be dropped first
-- The inline UNIQUE constraints create indexes named after the column
ALTER TABLE `users` DROP INDEX `email`;
ALTER TABLE `users` DROP INDEX `username`;
-- Discard and Import
ALTER TABLE `users` DISCARD TABLESPACE;
ALTER TABLE `users` IMPORT TABLESPACE;
-- Recreate indexes
ALTER TABLE `users` ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `users` ADD UNIQUE KEY `username` (`username`);

-- =====================================================
-- 2. EMPLOYEES TABLE
-- =====================================================
-- Drop foreign keys and indexes
ALTER TABLE `employees` DROP FOREIGN KEY `employees_user_id_foreign`;
ALTER TABLE `employees` DROP INDEX `employees_user_id_foreign`;
-- Discard and Import
ALTER TABLE `employees` DISCARD TABLESPACE;
ALTER TABLE `employees` IMPORT TABLESPACE;
-- Recreate
ALTER TABLE `employees` ADD KEY `employees_user_id_foreign` (`user_id`);
ALTER TABLE `employees` ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 3. CLIENTS TABLE
-- =====================================================
ALTER TABLE `clients` DROP FOREIGN KEY `clients_user_id_foreign`;
ALTER TABLE `clients` DROP INDEX `clients_user_id_foreign`;
ALTER TABLE `clients` DISCARD TABLESPACE;
ALTER TABLE `clients` IMPORT TABLESPACE;
ALTER TABLE `clients` ADD KEY `clients_user_id_foreign` (`user_id`);
ALTER TABLE `clients` ADD CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 4. CONTRACTED_CLIENTS TABLE
-- =====================================================
ALTER TABLE `contracted_clients` DROP FOREIGN KEY `contracted_clients_user_id_foreign`;
ALTER TABLE `contracted_clients` DROP INDEX `contracted_clients_user_id_foreign`;
ALTER TABLE `contracted_clients` DROP INDEX `name`;
ALTER TABLE `contracted_clients` DISCARD TABLESPACE;
ALTER TABLE `contracted_clients` IMPORT TABLESPACE;
ALTER TABLE `contracted_clients` ADD UNIQUE KEY `name` (`name`);
ALTER TABLE `contracted_clients` ADD KEY `contracted_clients_user_id_foreign` (`user_id`);
ALTER TABLE `contracted_clients` ADD CONSTRAINT `contracted_clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 5. LOCATIONS TABLE
-- =====================================================
ALTER TABLE `locations` DROP FOREIGN KEY `locations_contracted_client_id_foreign`;
ALTER TABLE `locations` DROP INDEX `locations_contracted_client_id_foreign`;
ALTER TABLE `locations` DISCARD TABLESPACE;
ALTER TABLE `locations` IMPORT TABLESPACE;
ALTER TABLE `locations` ADD KEY `locations_contracted_client_id_foreign` (`contracted_client_id`);
ALTER TABLE `locations` ADD CONSTRAINT `locations_contracted_client_id_foreign` FOREIGN KEY (`contracted_client_id`) REFERENCES `contracted_clients` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 6. CARS TABLE (no secondary indexes)
-- =====================================================
ALTER TABLE `cars` DISCARD TABLESPACE;
ALTER TABLE `cars` IMPORT TABLESPACE;

-- =====================================================
-- 7. OPTIMIZATION_RUNS TABLE (no secondary indexes initially)
-- =====================================================
ALTER TABLE `optimization_runs` DISCARD TABLESPACE;
ALTER TABLE `optimization_runs` IMPORT TABLESPACE;

-- =====================================================
-- 8. OPTIMIZATION_TEAMS TABLE
-- =====================================================
ALTER TABLE `optimization_teams` DROP FOREIGN KEY `optimization_teams_optimization_run_id_foreign`;
ALTER TABLE `optimization_teams` DROP FOREIGN KEY `optimization_teams_car_id_foreign`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_optimization_run_id_service_date_index`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_what_if_scenario_id_index`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_car_id_foreign`;
ALTER TABLE `optimization_teams` DISCARD TABLESPACE;
ALTER TABLE `optimization_teams` IMPORT TABLESPACE;
ALTER TABLE `optimization_teams` ADD KEY `optimization_teams_optimization_run_id_service_date_index` (`optimization_run_id`, `service_date`);
ALTER TABLE `optimization_teams` ADD KEY `optimization_teams_what_if_scenario_id_index` (`what_if_scenario_id`);
ALTER TABLE `optimization_teams` ADD KEY `optimization_teams_car_id_foreign` (`car_id`);
ALTER TABLE `optimization_teams` ADD CONSTRAINT `optimization_teams_optimization_run_id_foreign` FOREIGN KEY (`optimization_run_id`) REFERENCES `optimization_runs` (`id`) ON DELETE CASCADE;
ALTER TABLE `optimization_teams` ADD CONSTRAINT `optimization_teams_car_id_foreign` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;

-- =====================================================
-- 9. OPTIMIZATION_TEAM_MEMBERS TABLE
-- =====================================================
ALTER TABLE `optimization_team_members` DROP FOREIGN KEY `optimization_team_members_optimization_team_id_foreign`;
ALTER TABLE `optimization_team_members` DROP FOREIGN KEY `optimization_team_members_employee_id_foreign`;
ALTER TABLE `optimization_team_members` DROP INDEX `team_employee_unique`;
ALTER TABLE `optimization_team_members` DROP INDEX `optimization_team_members_employee_id_index`;
ALTER TABLE `optimization_team_members` DISCARD TABLESPACE;
ALTER TABLE `optimization_team_members` IMPORT TABLESPACE;
ALTER TABLE `optimization_team_members` ADD UNIQUE KEY `team_employee_unique` (`optimization_team_id`, `employee_id`);
ALTER TABLE `optimization_team_members` ADD KEY `optimization_team_members_employee_id_index` (`employee_id`);
ALTER TABLE `optimization_team_members` ADD CONSTRAINT `optimization_team_members_optimization_team_id_foreign` FOREIGN KEY (`optimization_team_id`) REFERENCES `optimization_teams` (`id`) ON DELETE CASCADE;
ALTER TABLE `optimization_team_members` ADD CONSTRAINT `optimization_team_members_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 10. TASKS TABLE
-- =====================================================
ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_location_id_foreign`;
ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_client_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_location_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_client_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_assigned_team_id_foreign`;
ALTER TABLE `tasks` DISCARD TABLESPACE;
ALTER TABLE `tasks` IMPORT TABLESPACE;
ALTER TABLE `tasks` ADD KEY `tasks_location_id_foreign` (`location_id`);
ALTER TABLE `tasks` ADD KEY `tasks_client_id_foreign` (`client_id`);
ALTER TABLE `tasks` ADD KEY `tasks_assigned_team_id_foreign` (`assigned_team_id`);
ALTER TABLE `tasks` ADD CONSTRAINT `tasks_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);
ALTER TABLE `tasks` ADD CONSTRAINT `tasks_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`);

-- =====================================================
-- 11. OPTIMIZATION_GENERATIONS TABLE
-- =====================================================
ALTER TABLE `optimization_generations` DROP FOREIGN KEY `optimization_generations_optimization_run_id_foreign`;
ALTER TABLE `optimization_generations` DROP INDEX `opt_gen_run_gen_idx`;
ALTER TABLE `optimization_generations` DISCARD TABLESPACE;
ALTER TABLE `optimization_generations` IMPORT TABLESPACE;
ALTER TABLE `optimization_generations` ADD KEY `opt_gen_run_gen_idx` (`optimization_run_id`, `generation_number`);
ALTER TABLE `optimization_generations` ADD CONSTRAINT `optimization_generations_optimization_run_id_foreign` FOREIGN KEY (`optimization_run_id`) REFERENCES `optimization_runs` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 12. ATTENDANCES TABLE
-- =====================================================
ALTER TABLE `attendances` DROP FOREIGN KEY `attendances_employee_id_foreign`;
ALTER TABLE `attendances` DROP INDEX `attendances_employee_id_foreign`;
ALTER TABLE `attendances` DISCARD TABLESPACE;
ALTER TABLE `attendances` IMPORT TABLESPACE;
ALTER TABLE `attendances` ADD KEY `attendances_employee_id_foreign` (`employee_id`);
ALTER TABLE `attendances` ADD CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 13. DAY_OFFS TABLE
-- =====================================================
ALTER TABLE `day_offs` DROP FOREIGN KEY `day_offs_employee_id_foreign`;
ALTER TABLE `day_offs` DROP FOREIGN KEY `day_offs_approved_by_foreign`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_employee_id_date_unique`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_date_index`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_status_index`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_approved_by_foreign`;
ALTER TABLE `day_offs` DISCARD TABLESPACE;
ALTER TABLE `day_offs` IMPORT TABLESPACE;
ALTER TABLE `day_offs` ADD UNIQUE KEY `day_offs_employee_id_date_unique` (`employee_id`, `date`);
ALTER TABLE `day_offs` ADD KEY `day_offs_date_index` (`date`);
ALTER TABLE `day_offs` ADD KEY `day_offs_status_index` (`status`);
ALTER TABLE `day_offs` ADD KEY `day_offs_approved_by_foreign` (`approved_by`);
ALTER TABLE `day_offs` ADD CONSTRAINT `day_offs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
ALTER TABLE `day_offs` ADD CONSTRAINT `day_offs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- =====================================================
-- 14. INVALID_TASKS TABLE
-- =====================================================
ALTER TABLE `invalid_tasks` DROP FOREIGN KEY `invalid_tasks_task_id_foreign`;
ALTER TABLE `invalid_tasks` DROP INDEX `invalid_tasks_task_id_index`;
ALTER TABLE `invalid_tasks` DROP INDEX `invalid_tasks_optimization_result_id_index`;
ALTER TABLE `invalid_tasks` DISCARD TABLESPACE;
ALTER TABLE `invalid_tasks` IMPORT TABLESPACE;
ALTER TABLE `invalid_tasks` ADD KEY `invalid_tasks_task_id_index` (`task_id`);
ALTER TABLE `invalid_tasks` ADD KEY `invalid_tasks_optimization_result_id_index` (`optimization_result_id`);
ALTER TABLE `invalid_tasks` ADD CONSTRAINT `invalid_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 15. SCENARIO_ANALYSES TABLE
-- =====================================================
ALTER TABLE `scenario_analyses` DROP INDEX `scenario_analyses_service_date_scenario_type_index`;
ALTER TABLE `scenario_analyses` DISCARD TABLESPACE;
ALTER TABLE `scenario_analyses` IMPORT TABLESPACE;
ALTER TABLE `scenario_analyses` ADD KEY `scenario_analyses_service_date_scenario_type_index` (`service_date`, `scenario_type`);

-- =====================================================
-- 16. JOBS TABLE
-- =====================================================
ALTER TABLE `jobs` DROP INDEX `jobs_queue_index`;
ALTER TABLE `jobs` DISCARD TABLESPACE;
ALTER TABLE `jobs` IMPORT TABLESPACE;
ALTER TABLE `jobs` ADD KEY `jobs_queue_index` (`queue`);

-- =====================================================
-- 17. CLIENT_APPOINTMENTS TABLE
-- =====================================================
ALTER TABLE `client_appointments` DROP FOREIGN KEY `client_appointments_client_id_foreign`;
ALTER TABLE `client_appointments` DROP FOREIGN KEY `client_appointments_assigned_team_id_foreign`;
ALTER TABLE `client_appointments` DROP FOREIGN KEY `client_appointments_recommended_team_id_foreign`;
ALTER TABLE `client_appointments` DROP FOREIGN KEY `client_appointments_approved_by_foreign`;
ALTER TABLE `client_appointments` DROP FOREIGN KEY `client_appointments_rejected_by_foreign`;
ALTER TABLE `client_appointments` DROP INDEX `client_appointments_client_id_foreign`;
ALTER TABLE `client_appointments` DROP INDEX `client_appointments_assigned_team_id_foreign`;
ALTER TABLE `client_appointments` DROP INDEX `client_appointments_recommended_team_id_foreign`;
ALTER TABLE `client_appointments` DROP INDEX `client_appointments_approved_by_foreign`;
ALTER TABLE `client_appointments` DROP INDEX `client_appointments_rejected_by_foreign`;
ALTER TABLE `client_appointments` DISCARD TABLESPACE;
ALTER TABLE `client_appointments` IMPORT TABLESPACE;
ALTER TABLE `client_appointments` ADD KEY `client_appointments_client_id_foreign` (`client_id`);
ALTER TABLE `client_appointments` ADD KEY `client_appointments_assigned_team_id_foreign` (`assigned_team_id`);
ALTER TABLE `client_appointments` ADD KEY `client_appointments_recommended_team_id_foreign` (`recommended_team_id`);
ALTER TABLE `client_appointments` ADD KEY `client_appointments_approved_by_foreign` (`approved_by`);
ALTER TABLE `client_appointments` ADD KEY `client_appointments_rejected_by_foreign` (`rejected_by`);
ALTER TABLE `client_appointments` ADD CONSTRAINT `client_appointments_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
ALTER TABLE `client_appointments` ADD CONSTRAINT `client_appointments_assigned_team_id_foreign` FOREIGN KEY (`assigned_team_id`) REFERENCES `optimization_teams` (`id`) ON DELETE SET NULL;
ALTER TABLE `client_appointments` ADD CONSTRAINT `client_appointments_recommended_team_id_foreign` FOREIGN KEY (`recommended_team_id`) REFERENCES `optimization_teams` (`id`) ON DELETE SET NULL;
ALTER TABLE `client_appointments` ADD CONSTRAINT `client_appointments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `client_appointments` ADD CONSTRAINT `client_appointments_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- =====================================================
-- 18. HOLIDAYS TABLE
-- =====================================================
ALTER TABLE `holidays` DROP FOREIGN KEY `holidays_created_by_foreign`;
ALTER TABLE `holidays` DROP INDEX `date`;
ALTER TABLE `holidays` DROP INDEX `holidays_created_by_foreign`;
ALTER TABLE `holidays` DISCARD TABLESPACE;
ALTER TABLE `holidays` IMPORT TABLESPACE;
ALTER TABLE `holidays` ADD UNIQUE KEY `date` (`date`);
ALTER TABLE `holidays` ADD KEY `holidays_created_by_foreign` (`created_by`);
ALTER TABLE `holidays` ADD CONSTRAINT `holidays_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 19. COMPANY_SETTINGS TABLE
-- =====================================================
ALTER TABLE `company_settings` DROP INDEX `key`;
ALTER TABLE `company_settings` DISCARD TABLESPACE;
ALTER TABLE `company_settings` IMPORT TABLESPACE;
ALTER TABLE `company_settings` ADD UNIQUE KEY `key` (`key`);

-- =====================================================
-- 20. NOTIFICATIONS TABLE
-- =====================================================
ALTER TABLE `notifications` DROP FOREIGN KEY `notifications_user_id_foreign`;
ALTER TABLE `notifications` DROP INDEX `notifications_user_id_read_at_index`;
ALTER TABLE `notifications` DISCARD TABLESPACE;
ALTER TABLE `notifications` IMPORT TABLESPACE;
ALTER TABLE `notifications` ADD KEY `notifications_user_id_read_at_index` (`user_id`, `read_at`);
ALTER TABLE `notifications` ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 21. FEEDBACK TABLE
-- =====================================================
ALTER TABLE `feedback` DROP FOREIGN KEY `feedback_client_id_foreign`;
ALTER TABLE `feedback` DROP INDEX `feedback_client_id_foreign`;
ALTER TABLE `feedback` DISCARD TABLESPACE;
ALTER TABLE `feedback` IMPORT TABLESPACE;
ALTER TABLE `feedback` ADD KEY `feedback_client_id_foreign` (`client_id`);
ALTER TABLE `feedback` ADD CONSTRAINT `feedback_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 22. QUOTATIONS TABLE
-- =====================================================
ALTER TABLE `quotations` DROP FOREIGN KEY `quotations_reviewed_by_foreign`;
ALTER TABLE `quotations` DROP FOREIGN KEY `quotations_quoted_by_foreign`;
ALTER TABLE `quotations` DROP FOREIGN KEY `quotations_appointment_id_foreign`;
ALTER TABLE `quotations` DROP FOREIGN KEY `quotations_converted_by_foreign`;
ALTER TABLE `quotations` DROP INDEX `quotations_booking_type_index`;
ALTER TABLE `quotations` DROP INDEX `quotations_status_index`;
ALTER TABLE `quotations` DROP INDEX `quotations_email_index`;
ALTER TABLE `quotations` DROP INDEX `quotations_created_at_index`;
ALTER TABLE `quotations` DROP INDEX `quotations_reviewed_by_foreign`;
ALTER TABLE `quotations` DROP INDEX `quotations_quoted_by_foreign`;
ALTER TABLE `quotations` DROP INDEX `quotations_appointment_id_foreign`;
ALTER TABLE `quotations` DROP INDEX `quotations_converted_by_foreign`;
ALTER TABLE `quotations` DISCARD TABLESPACE;
ALTER TABLE `quotations` IMPORT TABLESPACE;
ALTER TABLE `quotations` ADD KEY `quotations_booking_type_index` (`booking_type`);
ALTER TABLE `quotations` ADD KEY `quotations_status_index` (`status`);
ALTER TABLE `quotations` ADD KEY `quotations_email_index` (`email`);
ALTER TABLE `quotations` ADD KEY `quotations_created_at_index` (`created_at`);
ALTER TABLE `quotations` ADD KEY `quotations_reviewed_by_foreign` (`reviewed_by`);
ALTER TABLE `quotations` ADD KEY `quotations_quoted_by_foreign` (`quoted_by`);
ALTER TABLE `quotations` ADD KEY `quotations_appointment_id_foreign` (`appointment_id`);
ALTER TABLE `quotations` ADD KEY `quotations_converted_by_foreign` (`converted_by`);
ALTER TABLE `quotations` ADD CONSTRAINT `quotations_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `quotations` ADD CONSTRAINT `quotations_quoted_by_foreign` FOREIGN KEY (`quoted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `quotations` ADD CONSTRAINT `quotations_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `client_appointments` (`id`) ON DELETE SET NULL;
ALTER TABLE `quotations` ADD CONSTRAINT `quotations_converted_by_foreign` FOREIGN KEY (`converted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- =====================================================
-- 23. ALERTS TABLE
-- =====================================================
ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_task_id_foreign`;
ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_acknowledged_by_foreign`;
ALTER TABLE `alerts` DROP INDEX `alerts_task_id_alert_type_index`;
ALTER TABLE `alerts` DROP INDEX `alerts_acknowledged_by_foreign`;
ALTER TABLE `alerts` DISCARD TABLESPACE;
ALTER TABLE `alerts` IMPORT TABLESPACE;
ALTER TABLE `alerts` ADD KEY `alerts_task_id_alert_type_index` (`task_id`, `alert_type`);
ALTER TABLE `alerts` ADD KEY `alerts_acknowledged_by_foreign` (`acknowledged_by`);
ALTER TABLE `alerts` ADD CONSTRAINT `alerts_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
ALTER TABLE `alerts` ADD CONSTRAINT `alerts_acknowledged_by_foreign` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`);

-- =====================================================
-- 24. PERFORMANCE_FLAGS TABLE
-- =====================================================
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_task_id_foreign`;
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_employee_id_foreign`;
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_reviewed_by_foreign`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_task_id_flag_type_index`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_reviewed_index`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_employee_id_foreign`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_reviewed_by_foreign`;
ALTER TABLE `performance_flags` DISCARD TABLESPACE;
ALTER TABLE `performance_flags` IMPORT TABLESPACE;
ALTER TABLE `performance_flags` ADD KEY `performance_flags_task_id_flag_type_index` (`task_id`, `flag_type`);
ALTER TABLE `performance_flags` ADD KEY `performance_flags_reviewed_index` (`reviewed`);
ALTER TABLE `performance_flags` ADD KEY `performance_flags_employee_id_foreign` (`employee_id`);
ALTER TABLE `performance_flags` ADD KEY `performance_flags_reviewed_by_foreign` (`reviewed_by`);
ALTER TABLE `performance_flags` ADD CONSTRAINT `performance_flags_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
ALTER TABLE `performance_flags` ADD CONSTRAINT `performance_flags_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
ALTER TABLE `performance_flags` ADD CONSTRAINT `performance_flags_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`);

-- =====================================================
-- 25. EMPLOYEE_PERFORMANCE TABLE
-- =====================================================
ALTER TABLE `employee_performance` DROP FOREIGN KEY `employee_performance_employee_id_foreign`;
ALTER TABLE `employee_performance` DROP INDEX `employee_performance_employee_id_date_unique`;
ALTER TABLE `employee_performance` DROP INDEX `employee_performance_date_index`;
ALTER TABLE `employee_performance` DISCARD TABLESPACE;
ALTER TABLE `employee_performance` IMPORT TABLESPACE;
ALTER TABLE `employee_performance` ADD UNIQUE KEY `employee_performance_employee_id_date_unique` (`employee_id`, `date`);
ALTER TABLE `employee_performance` ADD KEY `employee_performance_date_index` (`date`);
ALTER TABLE `employee_performance` ADD CONSTRAINT `employee_performance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 26. COMPANY_CHECKLISTS TABLE
-- =====================================================
ALTER TABLE `company_checklists` DROP FOREIGN KEY `company_checklists_contracted_client_id_foreign`;
ALTER TABLE `company_checklists` DROP INDEX `company_checklists_contracted_client_id_foreign`;
ALTER TABLE `company_checklists` DISCARD TABLESPACE;
ALTER TABLE `company_checklists` IMPORT TABLESPACE;
ALTER TABLE `company_checklists` ADD KEY `company_checklists_contracted_client_id_foreign` (`contracted_client_id`);
ALTER TABLE `company_checklists` ADD CONSTRAINT `company_checklists_contracted_client_id_foreign` FOREIGN KEY (`contracted_client_id`) REFERENCES `contracted_clients` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 27. CHECKLIST_CATEGORIES TABLE
-- =====================================================
ALTER TABLE `checklist_categories` DROP FOREIGN KEY `checklist_categories_checklist_id_foreign`;
ALTER TABLE `checklist_categories` DROP INDEX `checklist_categories_checklist_id_foreign`;
ALTER TABLE `checklist_categories` DISCARD TABLESPACE;
ALTER TABLE `checklist_categories` IMPORT TABLESPACE;
ALTER TABLE `checklist_categories` ADD KEY `checklist_categories_checklist_id_foreign` (`checklist_id`);
ALTER TABLE `checklist_categories` ADD CONSTRAINT `checklist_categories_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `company_checklists` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 28. CHECKLIST_ITEMS TABLE
-- =====================================================
ALTER TABLE `checklist_items` DROP FOREIGN KEY `checklist_items_category_id_foreign`;
ALTER TABLE `checklist_items` DROP INDEX `checklist_items_category_id_foreign`;
ALTER TABLE `checklist_items` DISCARD TABLESPACE;
ALTER TABLE `checklist_items` IMPORT TABLESPACE;
ALTER TABLE `checklist_items` ADD KEY `checklist_items_category_id_foreign` (`category_id`);
ALTER TABLE `checklist_items` ADD CONSTRAINT `checklist_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `checklist_categories` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 29. TASK_CHECKLIST_COMPLETIONS TABLE
-- =====================================================
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_task_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_checklist_item_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_completed_by_foreign`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_task_id_checklist_item_id_unique`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_checklist_item_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_completed_by_foreign`;
ALTER TABLE `task_checklist_completions` DISCARD TABLESPACE;
ALTER TABLE `task_checklist_completions` IMPORT TABLESPACE;
ALTER TABLE `task_checklist_completions` ADD UNIQUE KEY `task_checklist_completions_task_id_checklist_item_id_unique` (`task_id`, `checklist_item_id`);
ALTER TABLE `task_checklist_completions` ADD KEY `task_checklist_completions_checklist_item_id_foreign` (`checklist_item_id`);
ALTER TABLE `task_checklist_completions` ADD KEY `task_checklist_completions_completed_by_foreign` (`completed_by`);
ALTER TABLE `task_checklist_completions` ADD CONSTRAINT `task_checklist_completions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
ALTER TABLE `task_checklist_completions` ADD CONSTRAINT `task_checklist_completions_checklist_item_id_foreign` FOREIGN KEY (`checklist_item_id`) REFERENCES `checklist_items` (`id`) ON DELETE CASCADE;
ALTER TABLE `task_checklist_completions` ADD CONSTRAINT `task_checklist_completions_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

-- =====================================================
-- 30. TASK_REVIEWS TABLE
-- =====================================================
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_task_id_foreign`;
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_contracted_client_id_foreign`;
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_reviewer_user_id_foreign`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_task_id_contracted_client_id_unique`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_rating_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_contracted_client_id_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_created_at_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_reviewer_user_id_foreign`;
ALTER TABLE `task_reviews` DISCARD TABLESPACE;
ALTER TABLE `task_reviews` IMPORT TABLESPACE;
ALTER TABLE `task_reviews` ADD UNIQUE KEY `task_reviews_task_id_contracted_client_id_unique` (`task_id`, `contracted_client_id`);
ALTER TABLE `task_reviews` ADD KEY `task_reviews_rating_index` (`rating`);
ALTER TABLE `task_reviews` ADD KEY `task_reviews_contracted_client_id_index` (`contracted_client_id`);
ALTER TABLE `task_reviews` ADD KEY `task_reviews_created_at_index` (`created_at`);
ALTER TABLE `task_reviews` ADD KEY `task_reviews_reviewer_user_id_foreign` (`reviewer_user_id`);
ALTER TABLE `task_reviews` ADD CONSTRAINT `task_reviews_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
ALTER TABLE `task_reviews` ADD CONSTRAINT `task_reviews_contracted_client_id_foreign` FOREIGN KEY (`contracted_client_id`) REFERENCES `contracted_clients` (`id`) ON DELETE CASCADE;
ALTER TABLE `task_reviews` ADD CONSTRAINT `task_reviews_reviewer_user_id_foreign` FOREIGN KEY (`reviewer_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 31. TRAINING_VIDEOS TABLE (no secondary indexes)
-- =====================================================
ALTER TABLE `training_videos` DISCARD TABLESPACE;
ALTER TABLE `training_videos` IMPORT TABLESPACE;

-- =====================================================
-- 32. EMPLOYEE_WATCHED_VIDEOS TABLE
-- =====================================================
ALTER TABLE `employee_watched_videos` DROP FOREIGN KEY `employee_watched_videos_user_id_foreign`;
ALTER TABLE `employee_watched_videos` DROP FOREIGN KEY `employee_watched_videos_training_video_id_foreign`;
ALTER TABLE `employee_watched_videos` DROP INDEX `employee_watched_videos_user_id_training_video_id_unique`;
ALTER TABLE `employee_watched_videos` DROP INDEX `employee_watched_videos_training_video_id_foreign`;
ALTER TABLE `employee_watched_videos` DISCARD TABLESPACE;
ALTER TABLE `employee_watched_videos` IMPORT TABLESPACE;
ALTER TABLE `employee_watched_videos` ADD UNIQUE KEY `employee_watched_videos_user_id_training_video_id_unique` (`user_id`, `training_video_id`);
ALTER TABLE `employee_watched_videos` ADD KEY `employee_watched_videos_training_video_id_foreign` (`training_video_id`);
ALTER TABLE `employee_watched_videos` ADD CONSTRAINT `employee_watched_videos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `employee_watched_videos` ADD CONSTRAINT `employee_watched_videos_training_video_id_foreign` FOREIGN KEY (`training_video_id`) REFERENCES `training_videos` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 33. PUSH_TOKENS TABLE
-- =====================================================
ALTER TABLE `push_tokens` DROP FOREIGN KEY `push_tokens_user_id_foreign`;
ALTER TABLE `push_tokens` DROP INDEX `token`;
ALTER TABLE `push_tokens` DROP INDEX `push_tokens_user_id_is_active_index`;
ALTER TABLE `push_tokens` DISCARD TABLESPACE;
ALTER TABLE `push_tokens` IMPORT TABLESPACE;
ALTER TABLE `push_tokens` ADD UNIQUE KEY `token` (`token`);
ALTER TABLE `push_tokens` ADD KEY `push_tokens_user_id_is_active_index` (`user_id`, `is_active`);
ALTER TABLE `push_tokens` ADD CONSTRAINT `push_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- =====================================================
-- 34. PERSONAL_ACCESS_TOKENS TABLE
-- =====================================================
ALTER TABLE `personal_access_tokens` DROP INDEX `token`;
ALTER TABLE `personal_access_tokens` DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`;
ALTER TABLE `personal_access_tokens` DISCARD TABLESPACE;
ALTER TABLE `personal_access_tokens` IMPORT TABLESPACE;
ALTER TABLE `personal_access_tokens` ADD UNIQUE KEY `token` (`token`);
ALTER TABLE `personal_access_tokens` ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`, `tokenable_id`);

-- =====================================================
-- 35. MIGRATIONS TABLE (no secondary indexes)
-- =====================================================
ALTER TABLE `migrations` DISCARD TABLESPACE;
ALTER TABLE `migrations` IMPORT TABLESPACE;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Recovery complete! Check for any errors above.' AS Status;
