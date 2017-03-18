<?php
namespace Shieldfy\Jury;

use Shieldfy\Config;
use Shieldfy\Exceptions\Exceptionable;
use Shieldfy\Exceptions\Exceptioner;
use Shieldfy\Exceptions\RulesNotFoundException;

use Shieldfy\Jury\Rule;

class Rules implements Exceptionable
{
    use Exceptioner;
    protected $ruleBag = null;
    protected $config = null;

    /**
     * [Construct Rules]
     * @param Config $config
     * @param string $name   [name of rules data]
     */
    public function __construct(Config $config, $name = '')
    {
        $this->config = $config;

        $bagFile = $config['rootDir'].'/data/'.$name.'.json';

        if(!file_exists($bagFile) || !is_readable($bagFile)){
            $this->throwException(new RulesNotFoundException);
        }

        //parse json file
        $rules = file_get_contents($bagFile);
        $decodedRules =  json_decode($rules,1);

        if(!$decodedRules || json_last_error() !== JSON_ERROR_NONE)
        {
            $this->throwException(new RulesNotFoundException);
        }

        $this->rules = $decodedRules;
    }

    /**
     * Build rules
     * @param  boolean $normalize [use normalizer before rule]
     * @return array   $rules
     */
    public function build($normalize = false)
    {
        $rules = [];
        foreach($this->rules as $id => $rule){
            $rules[] = new Rule($id,$rule,$normalize);
        }
        return $rules;
    }
}