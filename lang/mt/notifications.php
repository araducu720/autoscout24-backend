<?php

return [
    'payment_uploaded' => [
        'subject' => 'Prova ta\' ħlas riċevuta - Tranżazzjoni :reference',
        'message' => 'Il-prova ta\' ħlas tniżżlet u qed tistenna verifika',
        'admin' => [
            'subject' => 'Verifika ta\' ħlas meħtieġa - :reference',
            'message' => 'Prova ta\' ħlas ġdida teħtieġ verifika',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Ħlas ivverifikat - Il-fondi tiegħek huma siguri!',
            'message' => 'Il-ħlas għat-tranżazzjoni :reference ġie vverifikat',
            'intro' => 'Aħbarijiet tajba! Il-ħlas ġie vverifikat mit-tim tagħna.',
            'next_steps' => 'Issa tista\' tippjana l-għoti tal-vettura man-negozjant.',
        ],
        'dealer' => [
            'subject' => 'Ħlas ivverifikat - Vettura lesta għall-ġbir',
            'message' => 'Il-ħlas tiegħek għat-tranżazzjoni :reference ġie vverifikat',
            'intro' => 'Il-ħlas tiegħek ġie vverifikat b\'suċċess.',
            'next_steps' => 'Jekk jogħġbok ippjana l-ġbir tal-vettura mal-bejjiegħ.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Ġbir skedat għal :date',
        'message' => 'Il-ġbir tal-vettura ġie skedat',
        'seller' => [
            'intro' => 'In-negozjant skeda l-ġbir tal-vettura.',
        ],
        'dealer' => [
            'intro' => 'Il-ġbir ġie kkonfermat.',
        ],
    ],
    'pickup_proposed_subject' => 'Dati ta\' ġbir proposti għal :vehicle',
    'pickup_proposed_message' => 'Ġew proposti dati ta\' ġbir ġodda. Jekk jogħġbok irrevedi u kkonferma.',
    'pickup_confirmed_subject' => 'Ġbir ikkonfermat għal :vehicle',
    'pickup_confirmed_message' => 'Data ta\' ġbir ikkonfermata għal :date',
    'handover_confirmed' => [
        'subject' => 'Għoti tal-vettura kkonfermat',
        'message' => 'L-għoti tal-vettura ġie kkonfermat',
        'intro' => 'Il-vettura ngħatat b\'suċċess.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Tranżazzjoni kompluta - :reference',
            'message' => 'Il-bejgħ tal-vettura tiegħek tlesta b\'suċċess!',
            'greeting' => 'Prosit!',
            'intro' => 'Il-bejgħ tal-vettura tiegħek tlesta. Il-fondi se jiġu ttrasferiti fil-kont bankarju tiegħek.',
            'thanks' => 'Grazzi talli użajt AutoScout24 SafeTrade!',
        ],
        'dealer' => [
            'subject' => 'Tranżazzjoni kompluta - :reference',
            'message' => 'Ix-xiri tal-vettura tiegħek tlesta b\'suċċess!',
            'greeting' => 'Prosit!',
            'intro' => 'Ix-xiri tal-vettura tiegħek tlesta.',
            'thanks' => 'Grazzi talli użajt AutoScout24 SafeTrade!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Tranżazzjoni kkanċellata - :reference',
        'message' => 'It-tranżazzjoni :reference ġiet ikkanċellata',
        'intro' => 'Sfortunatament, din it-tranżazzjoni ġiet ikkanċellata.',
        'reason' => 'Raġuni: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Tilwima miftuħa - Tranżazzjoni :reference',
        'message' => 'Ġiet miftuħa tilwima għat-tranżazzjoni :reference',
        'intro' => 'Ġiet ippreżentata tilwima għal din it-tranżazzjoni.',
        'admin' => [
            'subject' => 'Tilwima ġdida teħtieġ attenzjoni - :reference',
            'message' => 'Tilwima ġdida nfetħet u teħtieġ reviżjoni',
        ],
    ],
    'common' => [
        'vehicle' => 'Vettura',
        'amount' => 'Ammont',
        'reference' => 'Referenza',
        'date' => 'Data',
        'status' => 'Status',
        'view_details' => 'Ara d-Dettalji',
        'contact_support' => 'Ikkuntattja s-Sapport',
        'questions' => 'Jekk għandek mistoqsijiet, jekk jogħġbok ikkuntattja t-tim ta\' sapport tagħna.',
    ],
];
