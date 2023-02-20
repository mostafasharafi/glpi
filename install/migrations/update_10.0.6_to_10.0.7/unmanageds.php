<?php

/**
 * ---------------------------------------------------------------------
 *
 * GLPI - Gestionnaire Libre de Parc Informatique
 *
 * http://glpi-project.org
 *
 * @copyright 2015-2023 Teclib' and contributors.
 * @copyright 2003-2014 by the INDEPNET Development Team.
 * @licence   https://www.gnu.org/licenses/gpl-3.0.html
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * ---------------------------------------------------------------------
 */

/**
 * @var DB $DB
 * @var Migration $migration
 */

if ($DB->fieldExists(\Unmanaged::getTable(), 'domains_id')) {
    $iterator = $DB->request([
        'SELECT' => ['id', 'domains_id'],
        'FROM'   => \Unmanaged::getTable(),
        'WHERE'  => ['domains_id' => ['>', 0]]
    ]);
    if (count($iterator)) {
        foreach ($iterator as $row) {
            $DB->insert("glpi_domains_items", [
                'domains_id'   => $row['domains_id'],
                'itemtype'     => 'Unmanaged',
                'items_id'     => $row['id']
            ]);
        }
    }
    $migration->dropField(\Unmanaged::getTable(), 'domains_id');
}

//new right value for unmanageds (previously based on config UPDATE)
$migration->addRight('unmanaged', READ | UPDATE | DELETE | PURGE, ['config' => UPDATE]);
