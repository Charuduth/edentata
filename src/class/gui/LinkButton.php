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

namespace hemio\edentata\gui;

use hemio\html;
use hemio\form;

/**
 * Description of LinkButton
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class LinkButton extends form\Container {

    public function __construct(\hemio\edentata\Request $url, $text) {
        $this['form'] = new html\Form();

        foreach ($url->get as $key => $value) {
            $input = new html\Input('hidden');
            $input->setAttribute('name', $key);
            $input->setAttribute('value', $value);
            $this['form']->addChild($input);
        }

        $this['form']['button'] = new html\Button();
        $this['form']['button']['text'] = new html\String($text);
    }

    public function setSuggested($suggested = true) {
        if ($suggested)
            $this['form']['button']->addCssClass('suggested');
        else
            $this['form']['button']->removeCssClass('suggested');
    }

    public function setBack($suggested = true) {
        if ($suggested)
            $this['form']['button']->addCssClass('back');
        else
            $this['form']['button']->removeCssClass('back');
    }
    
    public function setDisabled($disabled = true) {
        if ($disabled)
            $this['form']['button']->setAttribute('disabled', true);
        else
            $this['form']['button']->setAttribute('disabled', null);
    }

}