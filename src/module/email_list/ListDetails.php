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

namespace hemio\edentata\module\email_list;

use hemio\edentata\gui;
use hemio\form;

/**
 * Description of ListDetails
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class ListDetails extends \hemio\edentata\Window {

    public function content($list) {
        $window = $this->newFormWindow('select_subscribers', _('Email List'), $list);

        $window->addButtonRight(
                new gui\LinkButton(
                $this->module->request->derive('subscribers_create', true)
                , _('Add Subscriber')
                )
                , true);

        $this->subscribers($window, $list);

        return $window;
    }

    protected function subscribers(gui\WindowModuleWithForm $window, $list) {
        $subscribers = $this->db->subscriberSelect($list)->fetchAll();

        $fieldset = new gui\Fieldset(_('Subscribers'));
        $selectbox = new gui\Selectbox();

        $window->getForm()->addInheritableAppendage(
                'selected_subscribers', $selectbox
        );

        $window->getForm()->addChild($fieldset)->addChild($selectbox);

        foreach ($subscribers as $subscriber) {
            //todo add extra prefix outside of name logic
            $selectbox->addItem(
                    $subscriber['address']
                    , $subscriber['address']
                    , $subscriber['backend_status']
            );
        }
        $options = new \hemio\form\Container();

        $move = new \hemio\form\FieldSubmit('move', _('Move …'));
        $options[] = $move;
        $unsubscribe = new \hemio\form\FieldSubmit('unsubscribe', _('Unsubscribe'));
        $unsubscribe->getControlElement()->setAttribute('formaction', $this->module->request->derive('subscribers_unsubscribe', $list)->getUrl());
        $options[] = $unsubscribe;

        $selectbox->setOptions($options);
    }

}
