<?php

/*
 * Copyright (C) 2015 Michael Herold <quabla@hemio.de>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace hemio\edentata\module\email;

use hemio\edentata\sql;
use hemio\edentata\gui;

/**
 * Description of Overview
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class Overview extends \hemio\edentata\Window {

    public function overview() {
        $window = new \hemio\edentata\gui\Window(_('Email Accounts'));
        $window->addButton(
                new gui\LinkButton(
                $this->module->request->deriveArray('create'), _('New Email Address')
                )
        );

        $stmt = new sql\QuerySelectFunction($this->module->pdo, 'email.frontend_account');
        $res = $stmt->execute();

        $accounts = new gui\Listbox();
        while ($arr = $res->fetch(\PDO::FETCH_ASSOC)) {
            $address = $arr['local_part'] . '@' . $arr['domain'];
            $url = $this->module->request->derive('edit_account', $address);
            $accounts->addLink($url, $address);
        }

        $window->addChild($accounts);

        return $window;
    }

}
