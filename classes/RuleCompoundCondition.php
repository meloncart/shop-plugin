<?php namespace MelonCart\Shop\Classes;

use MelonCart\Shop\Models\PriceRuleCondition as PriceRuleConditionModel;
use RainLab\Notify\Interfaces\CompoundCondition as CompoundConditionInterface;

/**
 * Compound Condition Class
 */
class RuleCompoundCondition extends RuleConditionBase implements CompoundConditionInterface
{
    /**
     * Returns a condition title for displaying in the condition settings form
     * @return string
     */
    public function getTitle()
    {
        return 'Compound condition';
    }

    public function getText()
    {
        $result = $this->host->condition_type == 0
            ? 'ALL of subconditions should be '
            : 'ANY of subconditions should be ';

        $result .= $this->host->condition == 'false' ? 'FALSE' : 'TRUE';

        return $result;
    }

    public function getJoinText()
    {
        return $this->host->condition_type == 0 ? 'AND' : 'OR';
    }

    /**
     * Returns a list of condition types (`ConditionBase::TYPE_*` constants)
     * that can be added to this compound condition
     */
    public function getAllowedSubtypes()
    {
        return [];
    }

    public function defineFormFields()
    {
        return 'fields.yaml';
    }

    public function initConfigData($host)
    {
        $host->condition_type = 0;
        $host->condition = 'true';
    }

    public function getConditionOptions()
    {
        $options = [
            'true' => 'TRUE',
            'false' => 'FALSE'
        ];

        return $options;
    }

    public function getConditionTypeOptions()
    {
        $options = [
            '0' => 'ALL subconditions should meet the requirement',
            '1' => 'ANY subconditions should meet the requirement'
        ];

        return $options;
    }

    public function getChildOptions(array $options)
    {
        extract(array_merge([
            'ruleType' => RuleConditionBase::TYPE_ANY,
            'parentIds' => [],
        ], $options));

        $result = [
            'Compound condition' => RuleCompoundCondition::class
        ];

        if ($ruleType == PriceRuleConditionModel::TYPE_CATALOG) {
            $classes = self::findConditionsByType(RuleConditionBase::TYPE_PRODUCT);
            $result = $this->addClassesSubconditions($classes, $result);
        }
        elseif ($ruleType == PriceRuleConditionModel::TYPE_CART) {
            $rootTypeCondition = true;
            $containerObj = null;

            foreach ($parentIds as $parentId) {
                $parentObj = PriceRuleConditionModel::find($parentId);
                if ($parentObj && $parentObj->class_name != RuleCompoundCondition::class) {
                    $containerObj = $parentObj;
                    $rootTypeCondition = false;
                    break;
                }
            }

            if ($rootTypeCondition) {
                $allowedTypes = [
                    RuleConditionBase::TYPE_CART_ROOT,
                    RuleConditionBase::TYPE_CART_ATTRIBUTE
                ];
            }
            else {
                $allowedTypes = $containerObj->getAllowedSubtypes();
            }

            foreach ($allowedTypes as $type) {
                $classes = self::findConditionsByType($type);
                $result = $this->addClassesSubconditions($classes, $result);
            }
        }
        elseif ($ruleType == PriceRuleConditionModel::TYPE_CART_PRODUCTS) {
            $allowedTypes = [RuleConditionBase::TYPE_CART_ATTRIBUTE, RuleConditionBase::TYPE_PRODUCT];
            foreach ($allowedTypes as $type) {
                $classes = self::findConditionsByType($type);
                $result = $this->addClassesSubconditions($classes, $result);
            }
        }

        return $result;
    }

    protected function addClassesSubconditions($classes, $list)
    {
        foreach ($classes as $conditionClass => $obj) {

            $subConditions = $obj->listSubconditions();

            if ($subConditions) {
                $groupName = $obj->getGroupingTitle();

                foreach ($subConditions as $name => $subcondition) {
                    if (!$groupName) {
                        $list[$name] = $conditionClass.':'.$subcondition;
                    }
                    else {
                        if (!array_key_exists($groupName, $list)) {
                            $list[$groupName] = [];
                        }

                        $list[$groupName][$name] = $conditionClass.':'.$subcondition;
                    }
                }
            }
            else {
                $list[$obj->getName()] = $conditionClass;
            }
        }

        return $list;
    }

    /**
     * Checks whether the condition is TRUE for specified parameters
     * @param array $params Specifies a list of parameters as an associative array.
     * For example:
     *
     *     ['product' => object, 'shipping_method' => object]
     *
     * @return bool
     */
    public function isTrue(&$params)
    {
        $hostObj = $this->host;

        $requiredConditionValue = $hostObj->condition == 'true' ? true : false;

        foreach ($hostObj->children as $subcondition) {
            $subconditionResult = $subcondition->isTrue($params) ? true : false;

            /*
             * All
             */
            if ($hostObj->condition_type == 0) {
                if ($subconditionResult !== $requiredConditionValue) {
                    return false;
                }

            }
            /*
             * Any
             */
            else {
                if ($subconditionResult === $requiredConditionValue) {
                    return true;
                }
            }
        }

        /*
         * All
         */
        if ($hostObj->condition_type == 0) {
            return true;
        }

        return false;
    }
}
