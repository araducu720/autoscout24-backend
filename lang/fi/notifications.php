<?php

return [
    'payment_uploaded' => [
        'subject' => 'Maksutosite vastaanotettu - Kauppa :reference',
        'message' => 'Maksutosite on ladattu ja odottaa vahvistusta',
        'admin' => [
            'subject' => 'Maksun vahvistus vaaditaan - :reference',
            'message' => 'Uusi maksutosite vaatii vahvistuksen',
        ],
    ],
    'payment_verified' => [
        'seller' => [
            'subject' => 'Maksu vahvistettu - Varasi ovat turvassa!',
            'message' => 'Kaupan :reference maksu on vahvistettu',
            'intro' => 'Hienoja uutisia! Maksu on vahvistettu tiimimme toimesta.',
            'next_steps' => 'Voit nyt sopia ajoneuvon luovutuksesta jälleenmyyjän kanssa.',
        ],
        'dealer' => [
            'subject' => 'Maksu vahvistettu - Ajoneuvo on noudettavissa',
            'message' => 'Maksusi kaupasta :reference on vahvistettu',
            'intro' => 'Maksusi on vahvistettu onnistuneesti.',
            'next_steps' => 'Sovi ajoneuvon nouto myyjän kanssa.',
        ],
    ],
    'pickup_scheduled' => [
        'subject' => 'Nouto sovittu päivälle :date',
        'message' => 'Ajoneuvon nouto on sovittu',
        'seller' => [
            'intro' => 'Jälleenmyyjä on sopinut ajoneuvon noudon.',
        ],
        'dealer' => [
            'intro' => 'Nouto on vahvistettu.',
        ],
    ],
    'pickup_proposed_subject' => 'Noutopäivät ehdotettu ajoneuvolle :vehicle',
    'pickup_proposed_message' => 'Uudet noutopäivät on ehdotettu. Tarkista ja vahvista.',
    'pickup_confirmed_subject' => 'Nouto vahvistettu ajoneuvolle :vehicle',
    'pickup_confirmed_message' => 'Noutopäivä vahvistettu: :date',
    'handover_confirmed' => [
        'subject' => 'Ajoneuvon luovutus vahvistettu',
        'message' => 'Ajoneuvon luovutus on vahvistettu',
        'intro' => 'Ajoneuvo on luovutettu onnistuneesti.',
    ],
    'transaction_completed' => [
        'seller' => [
            'subject' => 'Kauppa valmis - :reference',
            'message' => 'Ajoneuvosi myynti on suoritettu onnistuneesti!',
            'greeting' => 'Onnittelut!',
            'intro' => 'Ajoneuvosi myynti on valmis. Varat siirretään pankkitilillesi.',
            'thanks' => 'Kiitos, että käytit AutoScout24 SafeTradea!',
        ],
        'dealer' => [
            'subject' => 'Kauppa valmis - :reference',
            'message' => 'Ajoneuvosi osto on suoritettu onnistuneesti!',
            'greeting' => 'Onnittelut!',
            'intro' => 'Ajoneuvosi osto on valmis.',
            'thanks' => 'Kiitos, että käytit AutoScout24 SafeTradea!',
        ],
    ],
    'transaction_cancelled' => [
        'subject' => 'Kauppa peruutettu - :reference',
        'message' => 'Kauppa :reference on peruutettu',
        'intro' => 'Valitettavasti tämä kauppa on peruutettu.',
        'reason' => 'Syy: :reason',
    ],
    'dispute_opened' => [
        'subject' => 'Riita-asia avattu - Kauppa :reference',
        'message' => 'Kaupasta :reference on avattu riita-asia',
        'intro' => 'Tästä kaupasta on tehty riita-asia.',
        'admin' => [
            'subject' => 'Uusi riita-asia vaatii huomiota - :reference',
            'message' => 'Uusi riita-asia on avattu ja vaatii tarkastelua',
        ],
    ],
    'common' => [
        'vehicle' => 'Ajoneuvo',
        'amount' => 'Summa',
        'reference' => 'Viite',
        'date' => 'Päivämäärä',
        'status' => 'Tila',
        'view_details' => 'Näytä tiedot',
        'contact_support' => 'Ota yhteyttä tukeen',
        'questions' => 'Jos sinulla on kysymyksiä, ota yhteyttä tukitiimiimme.',
    ],
];
