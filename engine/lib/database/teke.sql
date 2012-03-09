CREATE TABLE IF NOT EXISTS prefix_users (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(128) NOT NULL,
	facebook_id BIGINT(20) NOT NULL,
    first_name VARCHAR(255) DEFAULT '',
    last_name VARCHAR(255) DEFAULT '',
    email VARCHAR(255) DEFAULT NULL,
    language VARCHAR(6) NOT NULL DEFAULT 'et',
    registered DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    role INT(1) NOT NULL DEFAULT 1,
    last_login DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    PRIMARY KEY (id),
    UNIQUE INDEX username (username)
) ENGINE = InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_projects (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	goal text NOT NULL,
	start_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    end_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_project_members (
	project_id bigint(20) UNSIGNED NOT NULL,
	user_id bigint(20) UNSIGNED NOT NULL,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE,
	FOREIGN KEY (user_id) REFERENCES prefix_users (id) ON DELETE CASCADE,
	PRIMARY KEY project_member (project_id, user_id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_tasks (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	description text DEFAULT '',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_resources (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	description text DEFAULT '',
	url varchar(255) DEFAULT '',
	resource_type int(4) DEFAULT 1,
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_milestones (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	milestone_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_documents (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	url varchar(255) DEFAULT '',
	notes varchar(255) DEFAULT '',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_document_versions (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	document_id bigint(20) UNSIGNED NOT NULL,
	title varchar(255) NOT NULL,
	url varchar(255) DEFAULT '',
	notes varchar(255) DEFAULT '',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (document_id) REFERENCES prefix_documents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_project_comments (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	content varchar(255) NOT NULL,
	comment_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	updated DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

CREATE TABLE IF NOT EXISTS prefix_activity (
	id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	creator bigint(20) UNSIGNED NOT NULL,
	project_id bigint(20) UNSIGNED NOT NULL,
	activity_type enum('message', 'activity') DEFAULT 'activity',
	activity_subtype varchar(100) DEFAULT '',
	body text DEFAULT '',
	activity_data text DEFAULT '',
	created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE,
	FOREIGN KEY (project_id) REFERENCES prefix_projects (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- XXX consider a need of me --
CREATE TABLE IF NOT EXISTS prefix_images (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    creator bigint(20) UNSIGNED default NULL,
    name char(255) DEFAULT "",
    type char(255) DEFAULT "",
    size INT DEFAULT 0,
    width INT DEFAULT 0,
    height INT DEFAULT 0,
    location char(255) DEFAULT "",
    created TIMESTAMP DEFAULT NOW(),
    locked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
