ALTER TABLE `users` ADD COLUMN `id` SERIAL PRIMARY KEY FIRST;
ALTER TABLE `emails` ADD COLUMN `id` SERIAL PRIMARY KEY FIRST;
ALTER TABLE `users` ADD INDEX `validts` (`validts`);
ALTER TABLE `users` ADD INDEX `confirmed` (`confirmed`);
ALTER TABLE `users` ADD UNIQUE INDEX `email` (`email`);
ALTER TABLE `emails` ADD UNIQUE INDEX `email` (`email`);
ALTER TABLE `emails` ADD INDEX `valid` (`valid`);
