<?php

class Am_Plugin_AutoLockUser extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_VERSION = '5.5.4';

    protected $_configPrefix = 'misc.';

    public function _initSetupForm(Am_Form_Setup $form)
    {
        $form->addText('auto_lock_user.interval')
            ->setLabel("Invoice lifetime\nhow long (in days) keep invoice");
    }

    public function getTitle()
    {
        return 'Auto Lock User';
    }

    public function onDaily(Am_Event $event)
    {
        $day = $this->getConfig('auto_lock_user.interval');
        $date = date('Y-m-d', strtotime("-{$day} days"));
        $expired_bills = $this->getDi()->invoiceTable->findForRebill($date);

        foreach ($expired_bills as $invoice) {
            $user = $invoice->getUser();
            $user->lock(true);
            $this->logDebug(sprintf("USER %s HAS LOCKED ON %s, INVOICE : %s", $user->login, date('Y-m-d H:i:s'), $invoice->public_id));
        }
    }

}