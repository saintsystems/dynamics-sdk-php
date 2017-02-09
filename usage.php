<?php

// Retrieve all entities in the set
$odataClient->entitySet('leads')->get();

// Retrieve a single entity from an entitySet
$odataClient->entitySet('leads')->where('name','John')->first();
// OR
$odataClient->entitySet('leads')->filter('name','John')->top(1);

$odataClient->entitySet('leads')->filter('name','John')->value('emailaddress1');

$odataClient->entitySet('leads')->pluck('emailaddress1');

$odataClient->entitySet('leads')->pluck('emailaddress1', 'email');

$odataClient->entitySet('leads')->count();

$odataClient->entitySet('orders')->max('price');

$price = $odataClient->entitySet('orders')
                     ->where('finalized', 1)
                     ->avg('price');

// Selects
$leads = $odataClient->entitySet('leads')->select('firstname', 'lastname')->get();

// Where clauses
$leads = $odataClient->entitySet('leads')->where('votes', '=', 100)->get();
$leads = $odataClient->entitySet('leads')->filter('votes', '=', 100)->get();
$leads = $odataClient->entitySet('leads')->where('votes', 100)->get();

// Multiple conditions
$leads = $odataClient->entitySet('leads')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

// Or statements
$leads = $odataClient->entitySet('leads')
            ->where('votes', '>', 100)
            ->orWhere('name', 'John')
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('votes', '>', 100)
            ->or('name', 'John')
            ->get();

// contains
$leads = $odataClient->entitySet('leads')
            ->filter('name', 'contains', 'John')
            ->filter('name', '%', 'John')
            ->filter('name', '%_%', 'John')
            ->get();

// endswith
$leads = $odataClient->entitySet('leads')
            ->filter('name', 'endswith', 'John')
            ->filter('name', '%_', 'John')
            ->get();

// startswith
$leads = $odataClient->entitySet('leads')
            ->filter('name', 'startswith', 'John')
            ->filter('name', '_%', 'John')
            ->get();

// indexof
$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'indexof', 'John', '=', 1)
            ->get();

// length
$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'length', '=', 4)
            ->get();

// substring
$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'substring', 1, '=', 'ohn')
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'substring', 1, 2, '=', 'oh')
            ->get();

// tolower
$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'tolower', '=', 'microsoft')
            ->get();

// toupper
$leads = $odataClient->entitySet('leads')
            ->filter('firstname', 'toupper', '=', 'MICROSOFT')
            ->get();

// toupper
$leads = $odataClient->entitySet('leads')
            ->filter('company', 'trim', '=', 'Microsoft')
            ->get();

// toupper
$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'day', '=', 8)
            ->get();

// All leads born on the 8th day of the month
$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'day', '=', 8)
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'hour', '=', 4)
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'minute', '=', 30)
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'second', '=', 4)
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'month', '=', 40)
            ->get();

$leads = $odataClient->entitySet('leads')
            ->filter('birthday', 'year', '=', 1980)
            ->get();

$leads = $odataClient->entitySet('orders')
            ->filter('freight', 'ceiling', '=', 32)
            ->get();

$leads = $odataClient->entitySet('orders')
            ->filter('freight', 'floor', '=', 32)
            ->get();

$leads = $odataClient->entitySet('orders')
            ->filter('freight', 'round', '=', 32)
            ->get();

$dynamicsClient->entitySet('leads')->filter('Name','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','=','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','!=','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','>','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','>=','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','<','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','<=','Milk');
$dynamicsClient->entitySet('leads')->filter('Name','<=','Milk')->and();