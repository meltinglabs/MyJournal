<?php
/**
 * @package taxonomy
 */
class Terms extends xPDOSimpleObject {
    public function save($cacheFlag= null) {
        if ($this->isNew()) {
            $value = $this->get('value');
            $alias = $this->cleanAlias($value);
            $this->set('alias', $alias);
        }        
        $saved = parent :: save($cacheFlag);
        return $saved;
    }
    
    /**
     * Create an url friendly string for a taxonomy term.
     *
     * @param string $value The value to clean up
     * @return string The cleaned result string
     */
    public function cleanAlias( $value ){
        $value = $this->removeAccents($value); 
        $value = strtolower($value);
        // remove anything not alphanumeric OR "_"
        $value = preg_replace("/([^a-z0-9_\-]+)/i", '_', $value);
        // remove duplicate "_"
        $value = preg_replace("/(__+)/", '_', $value);
        // remove posible start/end "_"
        return trim($value, '_');
    }
    
    /**
     * Replaces non-english characters with safe english versions.
     * Submitted by deadelvis of CodeIgniter.com
     *
     * @param string $string The value to clean up
     * @param boolean $german Not used, will probably never be
     * @return string The cleaned result string
     */
    protected function removeAccents($string, $german = false) {
        // Single letters
        $single_fr = explode(" ", "À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280;"
        . " &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336;"
        . " &#340; &#344; Š &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý Ž &#377; &#379; à"
        . " á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì"
        . " í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345;"
        . " &#347; š &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ž &#378; &#380;");

        $single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O"
        . " O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i"
        . " i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");

        $single = array();
        for ($i=0; $i<count($single_fr); $i++) {
            $single[$single_fr[$i]] = $single_to[$i];
        }

        // Ligatures
        $ligatures = array("Æ"=>"Ae", "æ"=>"ae", "Œ"=>"Oe", "œ"=>"oe", "ß"=>"ss");
        // German umlauts
        $umlauts = array("Ä"=>"Ae", "ä"=>"ae", "Ö"=>"Oe", "ö"=>"oe", "Ü"=>"Ue", "ü"=>"ue");
        // Replace
        $replacements = array_merge($single, $ligatures);
        if ($german) $replacements = array_merge($replacements, $umlauts);
        $string = strtr($string, $replacements);
        return $string;
    }
}
?>