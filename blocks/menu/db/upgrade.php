<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the menu block database schema.
 *
 * @param int $oldversion
 * @param object|null $block
 * @return bool
 */
function xmldb_block_menu_upgrade($oldversion, $block = null) {
    global $DB;

    if ($oldversion < 2026110629) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('block_menu_exam_attempts');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);
        $table->add_field('attemptuid', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL);
        $table->add_field('examtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('total', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '100');
        $table->add_field('duration', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('language', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'en');
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $table->add_index('attemptuid', XMLDB_INDEX_UNIQUE, ['attemptuid']);
        $table->add_index('usercoursecompleted', XMLDB_INDEX_NOTUNIQUE, ['userid', 'courseid', 'timecompleted']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_block_savepoint(true, 2026110629, 'menu');
    }

    if ($oldversion < 2026110630) {
        upgrade_block_savepoint(true, 2026110630, 'menu');
    }

    if ($oldversion < 2026110631) {
        upgrade_block_savepoint(true, 2026110631, 'menu');
    }

    if ($oldversion < 2026110632) {
        upgrade_block_savepoint(true, 2026110632, 'menu');
    }

    if ($oldversion < 2026110633) {
        upgrade_block_savepoint(true, 2026110633, 'menu');
    }

    if ($oldversion < 2026110634) {
        upgrade_block_savepoint(true, 2026110634, 'menu');
    }

    if ($oldversion < 2026110635) {
        upgrade_block_savepoint(true, 2026110635, 'menu');
    }

    if ($oldversion < 2026110636) {
        upgrade_block_savepoint(true, 2026110636, 'menu');
    }

    if ($oldversion < 2026110637) {
        upgrade_block_savepoint(true, 2026110637, 'menu');
    }

    if ($oldversion < 2026110638) {
        upgrade_block_savepoint(true, 2026110638, 'menu');
    }

    if ($oldversion < 2026110639) {
        upgrade_block_savepoint(true, 2026110639, 'menu');
    }

    if ($oldversion < 2026110640) {
        upgrade_block_savepoint(true, 2026110640, 'menu');
    }

    if ($oldversion < 2026110641) {
        upgrade_block_savepoint(true, 2026110641, 'menu');
    }

    if ($oldversion < 2026110642) {
        upgrade_block_savepoint(true, 2026110642, 'menu');
    }

    if ($oldversion < 2026110643) {
        upgrade_block_savepoint(true, 2026110643, 'menu');
    }

    if ($oldversion < 2026110644) {
        upgrade_block_savepoint(true, 2026110644, 'menu');
    }

    if ($oldversion < 2026110645) {
        upgrade_block_savepoint(true, 2026110645, 'menu');
    }

    if ($oldversion < 2026110646) {
        upgrade_block_savepoint(true, 2026110646, 'menu');
    }

    if ($oldversion < 2026110647) {
        upgrade_block_savepoint(true, 2026110647, 'menu');
    }

    if ($oldversion < 2026110648) {
        upgrade_block_savepoint(true, 2026110648, 'menu');
    }

    if ($oldversion < 2026110649) {
        upgrade_block_savepoint(true, 2026110649, 'menu');
    }

    if ($oldversion < 2026110650) {
        upgrade_block_savepoint(true, 2026110650, 'menu');
    }

    if ($oldversion < 2026110651) {
        upgrade_block_savepoint(true, 2026110651, 'menu');
    }

    if ($oldversion < 2026110652) {
        upgrade_block_savepoint(true, 2026110652, 'menu');
    }

    if ($oldversion < 2026110653) {
        upgrade_block_savepoint(true, 2026110653, 'menu');
    }

    if ($oldversion < 2026110654) {
        upgrade_block_savepoint(true, 2026110654, 'menu');
    }

    if ($oldversion < 2026110655) {
        upgrade_block_savepoint(true, 2026110655, 'menu');
    }

    if ($oldversion < 2026110656) {
        upgrade_block_savepoint(true, 2026110656, 'menu');
    }

    if ($oldversion < 2026110657) {
        upgrade_block_savepoint(true, 2026110657, 'menu');
    }

    if ($oldversion < 2026110658) {
        upgrade_block_savepoint(true, 2026110658, 'menu');
    }

    if ($oldversion < 2026110659) {
        upgrade_block_savepoint(true, 2026110659, 'menu');
    }

    if ($oldversion < 2026110660) {
        upgrade_block_savepoint(true, 2026110660, 'menu');
    }

    if ($oldversion < 2026110661) {
        upgrade_block_savepoint(true, 2026110661, 'menu');
    }

    if ($oldversion < 2026110662) {
        upgrade_block_savepoint(true, 2026110662, 'menu');
    }

    if ($oldversion < 2026110663) {
        upgrade_block_savepoint(true, 2026110663, 'menu');
    }

    if ($oldversion < 2026110664) {
        upgrade_block_savepoint(true, 2026110664, 'menu');
    }

    if ($oldversion < 2026110665) {
        upgrade_block_savepoint(true, 2026110665, 'menu');
    }

    if ($oldversion < 2026110666) {
        upgrade_block_savepoint(true, 2026110666, 'menu');
    }

    if ($oldversion < 2026110667) {
        upgrade_block_savepoint(true, 2026110667, 'menu');
    }

    if ($oldversion < 2026110668) {
        upgrade_block_savepoint(true, 2026110668, 'menu');
    }

    if ($oldversion < 2026110669) {
        upgrade_block_savepoint(true, 2026110669, 'menu');
    }

    if ($oldversion < 2026110670) {
        upgrade_block_savepoint(true, 2026110670, 'menu');
    }

    if ($oldversion < 2026110671) {
        upgrade_block_savepoint(true, 2026110671, 'menu');
    }

    if ($oldversion < 2026110672) {
        upgrade_block_savepoint(true, 2026110672, 'menu');
    }

    if ($oldversion < 2026110673) {
        upgrade_block_savepoint(true, 2026110673, 'menu');
    }

    if ($oldversion < 2026110674) {
        upgrade_block_savepoint(true, 2026110674, 'menu');
    }

    return true;
}
