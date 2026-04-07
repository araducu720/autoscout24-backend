<?php
// Czech
return [
    'payment_uploaded' => ['subject' => 'Doklad o platbě přijat - Transakce :reference', 'message' => 'Doklad o platbě byl nahrán a čeká na ověření', 'admin' => ['subject' => 'Vyžadováno ověření platby - :reference', 'message' => 'Nový doklad o platbě vyžaduje ověření']],
    'payment_verified' => [
        'seller' => ['subject' => 'Platba ověřena - Vaše prostředky jsou zajištěny!', 'message' => 'Platba za transakci :reference byla ověřena', 'intro' => 'Skvělá zpráva! Platba byla ověřena naším týmem.', 'next_steps' => 'Nyní můžete naplánovat předání vozidla s prodejcem.'],
        'dealer' => ['subject' => 'Platba ověřena - Vozidlo připraveno k vyzvednutí', 'message' => 'Vaše platba za transakci :reference byla ověřena', 'intro' => 'Vaše platba byla úspěšně ověřena.', 'next_steps' => 'Prosím, naplánujte vyzvednutí vozidla s prodávajícím.'],
    ],
    'pickup_scheduled' => ['subject' => 'Vyzvednutí naplánováno na :date', 'message' => 'Vyzvednutí vozidla bylo naplánováno', 'seller' => ['intro' => 'Prodejce naplánoval vyzvednutí vozidla.'], 'dealer' => ['intro' => 'Vyzvednutí bylo potvrzeno.']],
    'pickup_proposed_subject' => 'Navrženy termíny vyzvednutí pro :vehicle',
    'pickup_proposed_message' => 'Byly navrženy nové termíny vyzvednutí. Prosím, zkontrolujte a potvrďte.',
    'pickup_confirmed_subject' => 'Vyzvednutí potvrzeno pro :vehicle',
    'pickup_confirmed_message' => 'Termín vyzvednutí potvrzen na :date',
    'handover_confirmed' => ['subject' => 'Předání vozidla potvrzeno', 'message' => 'Předání vozidla bylo potvrzeno', 'intro' => 'Vozidlo bylo úspěšně předáno.'],
    'transaction_completed' => [
        'seller' => ['subject' => 'Transakce dokončena - :reference', 'message' => 'Prodej vašeho vozidla byl úspěšně dokončen!', 'greeting' => 'Gratulujeme!', 'intro' => 'Prodej vašeho vozidla byl dokončen. Prostředky budou převedeny na váš bankovní účet.', 'thanks' => 'Děkujeme, že používáte AutoScout24 SafeTrade!'],
        'dealer' => ['subject' => 'Transakce dokončena - :reference', 'message' => 'Nákup vašeho vozidla byl úspěšně dokončen!', 'greeting' => 'Gratulujeme!', 'intro' => 'Nákup vašeho vozidla byl dokončen.', 'thanks' => 'Děkujeme, že používáte AutoScout24 SafeTrade!'],
    ],
    'transaction_cancelled' => ['subject' => 'Transakce zrušena - :reference', 'message' => 'Transakce :reference byla zrušena', 'intro' => 'Bohužel, tato transakce byla zrušena.', 'reason' => 'Důvod: :reason'],
    'dispute_opened' => ['subject' => 'Spor otevřen - Transakce :reference', 'message' => 'Byl otevřen spor pro transakci :reference', 'intro' => 'Pro tuto transakci byl podán spor.', 'admin' => ['subject' => 'Nový spor vyžaduje pozornost - :reference', 'message' => 'Byl otevřen nový spor, který vyžaduje přezkoumání']],
    'common' => ['vehicle' => 'Vozidlo', 'amount' => 'Částka', 'reference' => 'Reference', 'date' => 'Datum', 'status' => 'Stav', 'view_details' => 'Zobrazit podrobnosti', 'contact_support' => 'Kontaktovat podporu', 'questions' => 'Pokud máte jakékoli dotazy, kontaktujte prosím náš tým podpory.'],
];
