<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the General Public License (GPL 3.0)
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/GPL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @author      Jeroen Bleijenberg
 *
 * @copyright   Copyright (c) 2017
 * @license     http://opensource.org/licenses/GPL-3.0 General Public License (GPL 3.0)
 */

namespace Jcode;

use Jcode\Application\Config;
use Jcode\Layout\Block\Template;
use PHPMailer\PHPMailer\PHPMailer;

class Mail extends PHPMailer
{

    protected $template;

    protected $variables = [];

    /**
     * Set SMTP settings if enabled in application configuration
     * @return $this
     */
    public function init()
    {
        $config = Application::getConfig('mail');

        if ($config->getSmtp()) {
            $this->isSMTP();
            $this->Host       = $config->getHost();
            $this->Username   = $config->getUsername();
            $this->Password   = $config->getPassword();
            $this->SMTPSecure = $config->getSecure();
            $this->Port       = $config->getPost();
        }

        return $this;
    }

    public function addVariable($name, $value)
    {
        $this->variables[$name] = $value;

        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setSubject($subject)
    {
        $this->Subject = $subject;
    }

    public function send()
    {
        if ($this->getTemplate()) {
            $this->isHTML(true);

            /** @var Template $layout */
            $layout = Application::getClass('\Jcode\Layout\Block\Template');

            $layout->setTemplate($this->getTemplate());

            foreach ($this->variables as $key => $value) {
                $method = Config::convertStringToMethod($key);

                $layout->$method($value);
            }

            $layout->render();

            $this->Body = ob_get_clean();
        }

        parent::send();
    }
}