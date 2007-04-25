
new Report(array(
  'class' => 'Post',
  'target' => 'p',
  'exp' => new ExistsExpression(
    new Report(array(
      'collection' => new CollectionPathExpression('p', 'tags',' tag'),
      'exp' => new Condition(array(
           'exp1' => new AttrPathExpression('tag','name'),
           'operation' => '=',
           'exp2' => new ValueExpression("$tag")
         ))
      ))
    )
  ));
