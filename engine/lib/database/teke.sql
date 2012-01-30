-- XXX deleteme -- 
CREATE TABLE IF NOT EXISTS prefix_site (
    id bigint(20) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    url char(255) NOT NULL UNIQUE KEY,
    title char(255) NOT NULL,
    description TEXT,
    founder bigint(20) UNSIGNED DEFAULT NULL,
    installed BOOLEAN DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

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

-- XXX deleteme --
CREATE TABLE IF NOT EXISTS prefix_examinees (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(128) NOT NULL,
    password VARCHAR(40) NOT NULL,
    language VARCHAR(6) NOT NULL DEFAULT 'et',
    PRIMARY KEY (id),
    UNIQUE INDEX username (username)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- XXX deleteme --
CREATE TABLE IF NOT EXISTS prefix_userinfo (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    userid bigint(20) UNSIGNED default NULL UNIQUE KEY,
    firstname char(255) DEFAULT "",
    lastname char(255) DEFAULT "",
    email char(255) default NULL,
    language char(3) default 'EST',
    sex enum('boy','girl') default 'boy',
    homepage char(255) default NULL,
    FOREIGN KEY (userid) REFERENCES prefix_users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- XXX deleteme --
CREATE TABLE IF NOT EXISTS prefix_groups (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    creator bigint(20) UNSIGNED default NULL,
    name char(255),
    FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

-- XXX deleteme --
CREATE TABLE IF NOT EXISTS prefix_group_relations (
    group_id bigint(20) UNSIGNED NOT NULL,
    examinee_id bigint(20) UNSIGNED NOT NULL,
    FOREIGN KEY (examinee_id) REFERENCES prefix_examinees (id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES prefix_groups (id) ON DELETE CASCADE,
    PRIMARY KEY group_rel (group_id, examinee_id)
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;

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

-- XXX deleteme --
CREATE TABLE IF NOT EXISTS prefix_sounds (
    id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    creator bigint(20) UNSIGNED default NULL,
    name char(255) DEFAULT "",
    type char(255) DEFAULT "",
    size INT DEFAULT 0,
    location char(255) DEFAULT "",
    created TIMESTAMP DEFAULT NOW(),
    locked BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (creator) REFERENCES prefix_users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=UTF8;
