-- =====================================================
-- STEP 1: DROP INDEXES AND DISCARD ALL TABLESPACES
-- =====================================================
-- Run this FIRST, then copy your .ibd files, then run step 2
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. USERS TABLE
ALTER TABLE `users` DROP INDEX `email`;
ALTER TABLE `users` DROP INDEX `username`;
ALTER TABLE `users` DISCARD TABLESPACE;

-- 2. EMPLOYEES TABLE
ALTER TABLE `employees` DROP FOREIGN KEY `employees_user_id_foreign`;
ALTER TABLE `employees` DROP INDEX `employees_user_id_foreign`;
ALTER TABLE `employees` DISCARD TABLESPACE;

-- 3. CLIENTS TABLE
ALTER TABLE `clients` DROP FOREIGN KEY `clients_user_id_foreign`;
ALTER TABLE `clients` DROP INDEX `clients_user_id_foreign`;
ALTER TABLE `clients` DISCARD TABLESPACE;

-- 4. CONTRACTED_CLIENTS TABLE
ALTER TABLE `contracted_clients` DROP FOREIGN KEY `contracted_clients_user_id_foreign`;
ALTER TABLE `contracted_clients` DROP INDEX `contracted_clients_user_id_foreign`;
ALTER TABLE `contracted_clients` DROP INDEX `name`;
ALTER TABLE `contracted_clients` DISCARD TABLESPACE;

-- 5. LOCATIONS TABLE
ALTER TABLE `locations` DROP FOREIGN KEY `locations_contracted_client_id_foreign`;
ALTER TABLE `locations` DROP INDEX `locations_contracted_client_id_foreign`;
ALTER TABLE `locations` DISCARD TABLESPACE;

-- 6. CARS TABLE
ALTER TABLE `cars` DISCARD TABLESPACE;

-- 7. OPTIMIZATION_RUNS TABLE
ALTER TABLE `optimization_runs` DISCARD TABLESPACE;

-- 8. OPTIMIZATION_TEAMS TABLE
ALTER TABLE `optimization_teams` DROP FOREIGN KEY `optimization_teams_optimization_run_id_foreign`;
ALTER TABLE `optimization_teams` DROP FOREIGN KEY `optimization_teams_car_id_foreign`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_optimization_run_id_service_date_index`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_what_if_scenario_id_index`;
ALTER TABLE `optimization_teams` DROP INDEX `optimization_teams_car_id_foreign`;
ALTER TABLE `optimization_teams` DISCARD TABLESPACE;

-- 9. OPTIMIZATION_TEAM_MEMBERS TABLE
ALTER TABLE `optimization_team_members` DROP FOREIGN KEY `optimization_team_members_optimization_team_id_foreign`;
ALTER TABLE `optimization_team_members` DROP FOREIGN KEY `optimization_team_members_employee_id_foreign`;
ALTER TABLE `optimization_team_members` DROP INDEX `team_employee_unique`;
ALTER TABLE `optimization_team_members` DROP INDEX `optimization_team_members_employee_id_index`;
ALTER TABLE `optimization_team_members` DISCARD TABLESPACE;

-- 10. TASKS TABLE
ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_location_id_foreign`;
ALTER TABLE `tasks` DROP FOREIGN KEY `tasks_client_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_location_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_client_id_foreign`;
ALTER TABLE `tasks` DROP INDEX `tasks_assigned_team_id_foreign`;
ALTER TABLE `tasks` DISCARD TABLESPACE;

-- 11. OPTIMIZATION_GENERATIONS TABLE
ALTER TABLE `optimization_generations` DROP FOREIGN KEY `optimization_generations_optimization_run_id_foreign`;
ALTER TABLE `optimization_generations` DROP INDEX `opt_gen_run_gen_idx`;
ALTER TABLE `optimization_generations` DISCARD TABLESPACE;

-- 12. ATTENDANCES TABLE
ALTER TABLE `attendances` DROP FOREIGN KEY `attendances_employee_id_foreign`;
ALTER TABLE `attendances` DROP INDEX `attendances_employee_id_foreign`;
ALTER TABLE `attendances` DISCARD TABLESPACE;

-- 13. DAY_OFFS TABLE
ALTER TABLE `day_offs` DROP FOREIGN KEY `day_offs_employee_id_foreign`;
ALTER TABLE `day_offs` DROP FOREIGN KEY `day_offs_approved_by_foreign`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_employee_id_date_unique`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_date_index`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_status_index`;
ALTER TABLE `day_offs` DROP INDEX `day_offs_approved_by_foreign`;
ALTER TABLE `day_offs` DISCARD TABLESPACE;

-- 14. INVALID_TASKS TABLE
ALTER TABLE `invalid_tasks` DROP FOREIGN KEY `invalid_tasks_task_id_foreign`;
ALTER TABLE `invalid_tasks` DROP INDEX `invalid_tasks_task_id_index`;
ALTER TABLE `invalid_tasks` DROP INDEX `invalid_tasks_optimization_result_id_index`;
ALTER TABLE `invalid_tasks` DISCARD TABLESPACE;

-- 15. SCENARIO_ANALYSES TABLE
ALTER TABLE `scenario_analyses` DROP INDEX `scenario_analyses_service_date_scenario_type_index`;
ALTER TABLE `scenario_analyses` DISCARD TABLESPACE;

-- 16. JOBS TABLE
ALTER TABLE `jobs` DROP INDEX `jobs_queue_index`;
ALTER TABLE `jobs` DISCARD TABLESPACE;

-- 17. CLIENT_APPOINTMENTS TABLE
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

-- 18. HOLIDAYS TABLE
ALTER TABLE `holidays` DROP FOREIGN KEY `holidays_created_by_foreign`;
ALTER TABLE `holidays` DROP INDEX `date`;
ALTER TABLE `holidays` DROP INDEX `holidays_created_by_foreign`;
ALTER TABLE `holidays` DISCARD TABLESPACE;

-- 19. COMPANY_SETTINGS TABLE
ALTER TABLE `company_settings` DROP INDEX `key`;
ALTER TABLE `company_settings` DISCARD TABLESPACE;

-- 20. NOTIFICATIONS TABLE
ALTER TABLE `notifications` DROP FOREIGN KEY `notifications_user_id_foreign`;
ALTER TABLE `notifications` DROP INDEX `notifications_user_id_read_at_index`;
ALTER TABLE `notifications` DISCARD TABLESPACE;

-- 21. FEEDBACK TABLE
ALTER TABLE `feedback` DROP FOREIGN KEY `feedback_client_id_foreign`;
ALTER TABLE `feedback` DROP INDEX `feedback_client_id_foreign`;
ALTER TABLE `feedback` DISCARD TABLESPACE;

-- 22. QUOTATIONS TABLE
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

-- 23. ALERTS TABLE
ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_task_id_foreign`;
ALTER TABLE `alerts` DROP FOREIGN KEY `alerts_acknowledged_by_foreign`;
ALTER TABLE `alerts` DROP INDEX `alerts_task_id_alert_type_index`;
ALTER TABLE `alerts` DROP INDEX `alerts_acknowledged_by_foreign`;
ALTER TABLE `alerts` DISCARD TABLESPACE;

-- 24. PERFORMANCE_FLAGS TABLE
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_task_id_foreign`;
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_employee_id_foreign`;
ALTER TABLE `performance_flags` DROP FOREIGN KEY `performance_flags_reviewed_by_foreign`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_task_id_flag_type_index`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_reviewed_index`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_employee_id_foreign`;
ALTER TABLE `performance_flags` DROP INDEX `performance_flags_reviewed_by_foreign`;
ALTER TABLE `performance_flags` DISCARD TABLESPACE;

-- 25. EMPLOYEE_PERFORMANCE TABLE
ALTER TABLE `employee_performance` DROP FOREIGN KEY `employee_performance_employee_id_foreign`;
ALTER TABLE `employee_performance` DROP INDEX `employee_performance_employee_id_date_unique`;
ALTER TABLE `employee_performance` DROP INDEX `employee_performance_date_index`;
ALTER TABLE `employee_performance` DISCARD TABLESPACE;

-- 26. COMPANY_CHECKLISTS TABLE
ALTER TABLE `company_checklists` DROP FOREIGN KEY `company_checklists_contracted_client_id_foreign`;
ALTER TABLE `company_checklists` DROP INDEX `company_checklists_contracted_client_id_foreign`;
ALTER TABLE `company_checklists` DISCARD TABLESPACE;

-- 27. CHECKLIST_CATEGORIES TABLE
ALTER TABLE `checklist_categories` DROP FOREIGN KEY `checklist_categories_checklist_id_foreign`;
ALTER TABLE `checklist_categories` DROP INDEX `checklist_categories_checklist_id_foreign`;
ALTER TABLE `checklist_categories` DISCARD TABLESPACE;

-- 28. CHECKLIST_ITEMS TABLE
ALTER TABLE `checklist_items` DROP FOREIGN KEY `checklist_items_category_id_foreign`;
ALTER TABLE `checklist_items` DROP INDEX `checklist_items_category_id_foreign`;
ALTER TABLE `checklist_items` DISCARD TABLESPACE;

-- 29. TASK_CHECKLIST_COMPLETIONS TABLE
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_task_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_checklist_item_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP FOREIGN KEY `task_checklist_completions_completed_by_foreign`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_task_id_checklist_item_id_unique`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_checklist_item_id_foreign`;
ALTER TABLE `task_checklist_completions` DROP INDEX `task_checklist_completions_completed_by_foreign`;
ALTER TABLE `task_checklist_completions` DISCARD TABLESPACE;

-- 30. TASK_REVIEWS TABLE
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_task_id_foreign`;
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_contracted_client_id_foreign`;
ALTER TABLE `task_reviews` DROP FOREIGN KEY `task_reviews_reviewer_user_id_foreign`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_task_id_contracted_client_id_unique`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_rating_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_contracted_client_id_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_created_at_index`;
ALTER TABLE `task_reviews` DROP INDEX `task_reviews_reviewer_user_id_foreign`;
ALTER TABLE `task_reviews` DISCARD TABLESPACE;

-- 31. TRAINING_VIDEOS TABLE
ALTER TABLE `training_videos` DISCARD TABLESPACE;

-- 32. EMPLOYEE_WATCHED_VIDEOS TABLE
ALTER TABLE `employee_watched_videos` DROP FOREIGN KEY `employee_watched_videos_user_id_foreign`;
ALTER TABLE `employee_watched_videos` DROP FOREIGN KEY `employee_watched_videos_training_video_id_foreign`;
ALTER TABLE `employee_watched_videos` DROP INDEX `employee_watched_videos_user_id_training_video_id_unique`;
ALTER TABLE `employee_watched_videos` DROP INDEX `employee_watched_videos_training_video_id_foreign`;
ALTER TABLE `employee_watched_videos` DISCARD TABLESPACE;

-- 33. PUSH_TOKENS TABLE
ALTER TABLE `push_tokens` DROP FOREIGN KEY `push_tokens_user_id_foreign`;
ALTER TABLE `push_tokens` DROP INDEX `token`;
ALTER TABLE `push_tokens` DROP INDEX `push_tokens_user_id_is_active_index`;
ALTER TABLE `push_tokens` DISCARD TABLESPACE;

-- 34. PERSONAL_ACCESS_TOKENS TABLE
ALTER TABLE `personal_access_tokens` DROP INDEX `token`;
ALTER TABLE `personal_access_tokens` DROP INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`;
ALTER TABLE `personal_access_tokens` DISCARD TABLESPACE;

-- 35. MIGRATIONS TABLE
ALTER TABLE `migrations` DISCARD TABLESPACE;

SELECT 'STEP 1 COMPLETE! Now copy ALL your .ibd files to C:\\xampp\\mysql\\data\\opticrew\\ then run recovery_step2_import.sql' AS NextStep;
