<?php
/**
 * @package taxonomy
 */
class TermRelationships extends xPDOObject {
    public function save($cacheFlag= null) {
        $term = $this->get('term_id');
        $saved = parent :: save($cacheFlag);
        if($saved) $this->updateTaxonomy($term);
        return $saved;
    }
    
    public function remove(array $ancestors = array()) {
        $term = $this->get('term_id');
        $removed = parent :: remove($ancestors);
        if($removed) $this->updateTaxonomy($term);
        return $removed;
    }
    
    /**
     * Update taxonomy total on relationship saving. Remove taxonomy reference if there is no more relationship
     * @param integer $term The taxonomy term_id
     */
    public function updateTaxonomy($term){
        $query = $this->xpdo->newQuery('TermRelationships');
        $query->where(array(
            'term_id' => $term,
        ));
        $total = $this->xpdo->getCount('TermRelationships', $query);
        $taxonomy = $this->xpdo->getObject('TermTaxonomy', array(
            'term_id' => $term,
        ));
        if($taxonomy){
            if($total > 0){
                /* Update count */
                $taxonomy->set('count', $total);
                $taxonomy->save();
            } else {
                /* Remove all */
                $term = $taxonomy->getOne('Term');
                $term->remove();
            }
        }        
    }
}
?>