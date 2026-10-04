-- Himalayan Education Pathways: empties the content tables before re-importing content-data.sql.
-- Clears sections, section_items, services (courses), posts (blog) and the media attached to them.
-- Uploaded files on disk are not touched.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `section_items`;
DELETE FROM `sections`;
DELETE FROM `services`;
DELETE FROM `posts`;
DELETE FROM `media` WHERE `model_type` IN (
    'App\\Models\\Section',
    'App\\Models\\SectionItem',
    'App\\Modules\\Service\\Models\\Service',
    'App\\Modules\\Post\\Models\\Post'
);

SET FOREIGN_KEY_CHECKS = 1;
