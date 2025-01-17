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

namespace hemio\edentata\module\web;

use hemio\edentata\gui;
use hemio\form;
use hemio\edentata\exception;

/**
 * Description of HttpsCert
 *
 * @author Michael Herold <quabla@hemio.de>
 */
class HttpsCert extends Window
{

    public function content($address, $identifier)
    {
        $domain = Utils::getHost($address);
        $port   = Utils::getPort($address);

        $window = $this->newFormWindow(
            'https_cert'
            , _('Supply Certificate')
            , $address
            , _('Supply Certificate')
        );

        $certData = $this->db->httpsSelectSingle($domain, $port, $identifier)->fetch();

        $requestObj = new CertRequest($certData['x509_request']);

        $request = new gui\Fieldset(_('Certificate Signing Request (CSR)'));

        $request->addChild(new gui\Hint(
            _('A CSR enables to request a SSL certificate from a certificate authority.'
                .' The issued certificate from the certificate authority must be supplied below.')));

        $request->addChild(
            new gui\Output('Common Name (CN)', $requestObj->commonName()));

        $pre = new gui\Pre($requestObj->formatted());
        $request->addChild($pre);

        $certFieldset = new gui\Fieldset(_('Certificate (PEM Format)'));
        $cert         = new form\FieldTextarea('x509_certificate',
                                               _('Certificate'));
        $cert->addInheritableAppendage(form\Abstract_\Form::FORM_FIELD_TEMPLATE,
                                       new form\template\FormPlainControl());
        $cert->getControlElement()->addCssClass('pre_content');
        $cert->getControlElement()->setCssProperty('height', '50em');
        $cert->getControlElement()->setAttribute('placeholder',
                                                 '-----BEGIN CERTIFICATE-----');

        if ($certData['x509_certificate']) {
            $certObj = new Cert($cert['x509_certificate']);
            $cert->setDefaultValue($certObj->formatted());
        }

        $window->getForm()->addChild($request);
        $window->getForm()->addChild($certFieldset)->addChild($cert);

        $this->handleSubmit($window->getForm(), $cert, $domain, $port,
                            $identifier);

        return $window;
    }

    protected function handleSubmit(
    gui\FormPost $form
    , form\FieldTextarea $cert
    , $domain
    , $port
    , $identifier
    )
    {
        if ($form->correctSubmitted()) {
            $certs = Cert::extract($cert->getValueUser());
            if (count($certs) !== 1)
                throw new exception\Error('Expecting exactly one cert');

            $crt = array_pop($certs);

            $params = [
                'p_domain' => $domain,
                'p_port' => $port,
                'p_identifier' => $identifier,
                'p_authority_key_identifier' => $crt->authorityKeyIdentifier(),
                'p_x509_certificate' => $crt->raw()
            ];

            $this->db->httpsUpdate($params);

            throw new exception\Successful;
        }
    }
}
