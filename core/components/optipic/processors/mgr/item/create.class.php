<?php

class OptiPicImageCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'OptiPicImage';
    public $classKey = 'OptiPicImage';
    public $languageTopics = ['optipic'];
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $name = trim($this->getProperty('file'));
        if (empty($name)) {
            $this->modx->error->addField('file', $this->modx->lexicon('optipic_item_err_name'));
        } elseif ($this->modx->getCount($this->classKey, ['file' => $name])) {
            $this->modx->error->addField('file', $this->modx->lexicon('optipic_item_err_ae'));
        }

        return parent::beforeSet();
    }

}

return 'OptiPicImageCreateProcessor';