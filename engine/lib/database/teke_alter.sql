/* 09.03.2012 */
ALTER TABLE prefix_documents ADD COLUMN notes varchar(255) DEFAULT '';
ALTER TABLE prefix_document_versions ADD COLUMN notes varchar(255) DEFAULT '';

/* 12.03.2012 */
ALTER TABLE prefix_milestones ADD COLUMN flag_color int(4) DEFAULT 1;
ALTER TABLE prefix_milestones ADD COLUMN notes varchar(255) DEFAULT '';
