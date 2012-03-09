/* 09.03.2012 */
ALTER TABLE prefix_documents ADD COLUMN notes varchar(255) DEFAULT '';
ALTER TABLE prefix_document_versions ADD COLUMN notes varchar(255) DEFAULT '';
