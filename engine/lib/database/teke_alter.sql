/* 09.03.2012 */
ALTER TABLE prefix_documents ADD COLUMN notes varchar(255) DEFAULT '';
ALTER TABLE prefix_document_versions ADD COLUMN notes varchar(255) DEFAULT '';

/* 12.03.2012 */
ALTER TABLE prefix_milestones ADD COLUMN flag_color int(4) DEFAULT 1;
ALTER TABLE prefix_milestones ADD COLUMN notes varchar(255) DEFAULT '';

/* 26.03.2012 */
ALTER TABLE prefix_tasks ADD COLUMN start_date DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE prefix_tasks ADD COLUMN end_date DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE prefix_tasks ADD COLUMN is_timelined TINYINT(1) DEFAULT 0;

/* 29.03.2012 */
ALTER TABLE prefix_documents ADD COLUMN is_active TINYINT(1) DEFAULT 1;
ALTER TABLE prefix_documents ADD COLUMN end_date DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE prefix_document_versions ADD COLUMN version_type int(4) DEFAULT 1;
