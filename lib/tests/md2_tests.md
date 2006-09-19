test_func:
 - in: [ C2 ]
   with:
   do : >
      return 'C2';
 - in: [ C1 ]
   with:
   do : >
      return 'C1';
 - with: {&$c1 : C1, &$c2 : C2}
   do : |
      return 'C1C2';
 - with: {&$c1 : C1, &$c2 : C1 }
   do: |
      return 'C1C1';

test_func2:
 - in: [ C1, C1 ]
   with:
   do : >
      return 'C1C1';
 - in: [ C2, C1 ]
   with:
   do : >
      return 'C2C1';
 - in: [ C2 ]
   with:
   do : >
      return 'C2';
 - in: [ C1 ]
   with:
   do : >
      return 'C1';
 - in: [ C1, C1, C1 ]
   with:
   do : >
      return 'C1C1C1';
 - in: [ C1, C2, C1 ]
   with:
   do : >
      return 'C1C2C1';
 - with: {&$c1 : C1, &$c2 : C2}
   do : |
      return 'C1C2';
 - with: {&$c1 : C1, &$c2 : C1 }
   do: |
      return 'C1C1';


test_comp_func:
  - in: [ C3, C2, C1 ]
    with:
    do : |
      return 'OK';
  - in: [ C3, C2, C1 ]
    with: { &$c1 : C1, &$c2 : C2 }
    do : |
      return 'OK';

