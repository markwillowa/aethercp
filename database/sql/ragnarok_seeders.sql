-- =====================================================================
-- Ragnarok Online Emulator Game Database Starter Seeders
-- Target Database: Ragnarok Game Database (`freynix` / `ragnarok`)
-- Compatible with: rAthena & Hercules (Renewal & Pre-Renewal)
-- Generated for AetherCP Control Panel Integration
-- =====================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- -----------------------------------------------------
-- Dumping starter game accounts for table `login`
-- Default passwords:
--   admin    -> Admin1234!
--   malda009 -> Maki0913$
--   s1       -> athena
--   players  -> Password123!
-- -----------------------------------------------------

INSERT INTO `login` (`account_id`, `userid`, `user_pass`, `sex`, `email`, `group_id`, `state`, `unban_time`, `expiration_time`, `logincount`, `lastlogin`, `last_ip`, `birthdate`, `character_slots`, `pincode`, `pincode_change`, `vip_time`, `old_group`, `web_auth_token`, `web_auth_token_enabled`) VALUES
(1, 's1', 'athena', 'M', 'athena@athena.com', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000000, 'admin', 'Admin1234!', 'M', 'admin@freynix.local', 99, 0, 0, 0, 5, NOW(), '127.0.0.1', '1990-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000001, 'malda009', 'Maki0913$', 'M', 'owner@freynix.local', 99, 0, 0, 0, 1, NOW(), '127.0.0.1', '1995-09-13', 9, '', 0, 0, 0, NULL, 0),
(2000002, 'rune_smith', 'Password123!', 'M', 'runesmith@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000003, 'kafra_trader', 'Password123!', 'F', 'trader@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000004, 'card_collector', 'Password123!', 'M', 'cards@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0),
(2000005, 'alchemist_pro', 'Password123!', 'F', 'alchemist@aether.local', 0, 0, 0, 0, 0, NULL, '127.0.0.1', '2000-01-01', 9, '', 0, 0, 0, NULL, 0)
ON DUPLICATE KEY UPDATE
    `group_id` = VALUES(`group_id`),
    `email` = VALUES(`email`),
    `character_slots` = VALUES(`character_slots`);

-- -----------------------------------------------------
-- Dumping starter characters for table `char`
-- -----------------------------------------------------

INSERT INTO `char` (`char_id`, `account_id`, `char_num`, `name`, `class`, `base_level`, `job_level`, `base_exp`, `job_exp`, `zeny`, `str`, `agi`, `vit`, `int`, `dex`, `luk`, `max_hp`, `hp`, `max_sp`, `sp`, `status_point`, `skill_point`, `hair`, `hair_color`, `clothes_color`, `last_map`, `last_x`, `last_y`, `save_map`, `save_x`, `save_y`, `online`, `sex`) VALUES
(150001, 2000000, 0, 'Freynix', 4013, 99, 70, 0, 0, 50000000, 99, 90, 80, 40, 80, 30, 15000, 15000, 1200, 1200, 100, 50, 1, 2, 0, 'prontera', 156, 191, 'prontera', 156, 191, 0, 'M'),
(150002, 2000002, 0, 'RuneSmith', 4012, 99, 70, 0, 0, 50000000, 99, 90, 80, 40, 80, 30, 15000, 15000, 1200, 1200, 100, 50, 2, 3, 0, 'prontera', 156, 191, 'prontera', 156, 191, 0, 'M'),
(150003, 2000003, 0, 'KafraTrader', 4015, 99, 70, 0, 0, 50000000, 99, 90, 80, 40, 80, 30, 18000, 18000, 1100, 1100, 100, 50, 3, 1, 0, 'prontera', 156, 191, 'prontera', 156, 191, 0, 'F'),
(150004, 2000004, 0, 'CardCollector', 4016, 99, 70, 0, 0, 50000000, 99, 90, 80, 40, 80, 30, 16000, 16000, 1300, 1300, 100, 50, 4, 4, 0, 'prontera', 156, 191, 'prontera', 156, 191, 0, 'M'),
(150005, 2000005, 0, 'AlchemistPro', 4018, 99, 70, 0, 0, 50000000, 99, 90, 80, 40, 80, 30, 14000, 14000, 1400, 1400, 100, 50, 5, 2, 0, 'prontera', 156, 191, 'prontera', 156, 191, 0, 'F')
ON DUPLICATE KEY UPDATE
    `zeny` = VALUES(`zeny`),
    `base_level` = VALUES(`base_level`),
    `job_level` = VALUES(`job_level`),
    `online` = VALUES(`online`);

-- -----------------------------------------------------
-- Dumping starter Cash Points for table `acc_reg_num`
-- -----------------------------------------------------

INSERT INTO `acc_reg_num` (`account_id`, `key`, `index`, `value`) VALUES
(2000000, '#CASHPOINTS', 0, 50000),
(2000001, '#CASHPOINTS', 0, 50000),
(2000002, '#CASHPOINTS', 0, 50000),
(2000003, '#CASHPOINTS', 0, 50000),
(2000004, '#CASHPOINTS', 0, 50000),
(2000005, '#CASHPOINTS', 0, 50000)
ON DUPLICATE KEY UPDATE
    `value` = VALUES(`value`);

-- -----------------------------------------------------
-- Dumping starter Kafra Storage items for table `storage`
-- -----------------------------------------------------

DELETE FROM `storage` WHERE `account_id` IN (2000000, 2000002, 2000003, 2000004, 2000005);

INSERT INTO `storage` (`account_id`, `nameid`, `amount`, `equip`, `identify`, `refine`, `attribute`, `card0`, `card1`, `card2`, `card3`, `expire_time`, `bound`, `unique_id`) VALUES
(2000000, 1107, 1, 0, 1, 7, 0, 0, 0, 0, 0, 0, 0, 0), -- +7 Blade
(2000000, 2101, 1, 0, 1, 6, 0, 0, 0, 0, 0, 0, 0, 0), -- +6 Guard
(2000000, 501, 500, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), -- 500x Red Potion
(2000000, 504, 250, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), -- 250x White Potion
(2000000, 607, 50, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), -- 50x Yggdrasil Berry
(2000000, 984, 100, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), -- 100x Oridecon
(2000000, 985, 100, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0), -- 100x Elunium
(2000000, 4001, 2, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0); -- 2x Poring Card

COMMIT;
SET FOREIGN_KEY_CHECKS=1;
