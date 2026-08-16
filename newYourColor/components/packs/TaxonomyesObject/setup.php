<?php  function ExtractValues($data){
  $TaxonomyesObject = array();
  foreach (TaxonomyesObject() as $tkey => $tmeky) {
    $TaxonomyesObject[$tkey] = $tmeky->label;
  } 
  return $TaxonomyesObject;
}
function TaxonomyesObject($data=array(),$values=false){
  $args = array(
    'public'   => true,  
  ); 
  if(isset($data['output'])){
    $output = $data['output']; // or objects
  }else{
    $output = 'objects'; // or objects
  }
  if(isset($data['operator'])){
    $operator = $data['operator']; // or objects
  }else{
    $operator = 'and'; // or objects
  }
  $Ret = array();
  if(!isset($data['excloded'])) $data['excloded'] = array('post_format');
  $taxonomies = get_taxonomies( $args, $output, $operator ); 
  if ( $taxonomies ) {
    foreach ( $taxonomies  as $taxonomy ) {
      if(!in_array($taxonomy->name,$data['excloded'])){
        $Ret[$taxonomy->name] = $taxonomy;
      }
    }
  }
  if($values != false){
    $Ret = ExtractValues($Ret);
  }
  return $Ret;
}