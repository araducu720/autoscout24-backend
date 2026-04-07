@extends('pdf.layout')

@section('title', 'Sales Contract')
@section('document-type', 'SALES CONTRACT')

@section('content')
<h1>Vehicle Sales Contract</h1>

<div class="info-box highlight">
    <strong>Transaction Reference:</strong> <span class="reference">{{ $transaction->reference }}</span><br>
    <strong>Date:</strong> {{ $generatedAt->format('d.m.Y') }}
</div>

<div class="two-column">
    <div class="column">
        <h2>Seller Information</h2>
        <table>
            <tr>
                <td><strong>Name:</strong></td>
                <td>{{ $seller->name }}</td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td>{{ $seller->email }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong></td>
                <td>{{ $seller->phone ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="column">
        <h2>Buyer Information (Dealer)</h2>
        <table>
            <tr>
                <td><strong>Company:</strong></td>
                <td>{{ $dealer->company_name }}</td>
            </tr>
            <tr>
                <td><strong>Contact:</strong></td>
                <td>{{ $dealer->user->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Address:</strong></td>
                <td>{{ $dealer->address ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>City:</strong></td>
                <td>{{ $dealer->city ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>
</div>

<h2>Vehicle Details</h2>
<table>
    <tr>
        <th colspan="2">Vehicle Information</th>
    </tr>
    <tr>
        <td><strong>Make / Model:</strong></td>
        <td>{{ $listing->make->name ?? '' }} {{ $listing->model->name ?? '' }}</td>
    </tr>
    <tr>
        <td><strong>Year:</strong></td>
        <td>{{ $listing->year }}</td>
    </tr>
    <tr>
        <td><strong>Mileage:</strong></td>
        <td>{{ number_format($listing->mileage) }} km</td>
    </tr>
    <tr>
        <td><strong>VIN:</strong></td>
        <td>{{ $listing->vin ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>License Plate:</strong></td>
        <td>{{ $listing->license_plate ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>First Registration:</strong></td>
        <td>{{ $listing->first_registration ? $listing->first_registration->format('d.m.Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Fuel Type:</strong></td>
        <td>{{ ucfirst($listing->fuel_type ?? 'N/A') }}</td>
    </tr>
    <tr>
        <td><strong>Transmission:</strong></td>
        <td>{{ ucfirst($listing->transmission ?? 'N/A') }}</td>
    </tr>
    <tr>
        <td><strong>Color:</strong></td>
        <td>{{ $listing->color ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Condition:</strong></td>
        <td>{{ ucfirst($listing->condition ?? 'N/A') }}</td>
    </tr>
</table>

<h2>Transaction Details</h2>
<div class="info-box success">
    <table>
        <tr>
            <td><strong>Agreed Purchase Price:</strong></td>
            <td class="amount">€{{ number_format($transaction->amount, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>Payment Method:</strong></td>
            <td>{{ $transaction->payment_method === 'bank_transfer' ? 'Bank Transfer' : 'Leasing' }}</td>
        </tr>
        <tr>
            <td><strong>Payment Status:</strong></td>
            <td>{{ ucfirst(str_replace('_', ' ', $transaction->status)) }}</td>
        </tr>
    </table>
</div>

<h2>Terms and Conditions</h2>
<ol>
    <li>The seller confirms that they are the legal owner of the vehicle and have the right to sell it.</li>
    <li>The vehicle is sold "as is" based on the condition described in the listing.</li>
    <li>The buyer agrees to complete the payment within the specified timeframe.</li>
    <li>The vehicle handover will occur after payment verification by AutoScout24.</li>
    <li>Both parties agree to the AutoScout24 SafeTrade terms of service.</li>
    <li>This contract is legally binding upon confirmation of the SafeTrade transaction.</li>
</ol>

<div class="two-column mt-20">
    <div class="column">
        <div class="signature-box">
            <strong>Seller Signature:</strong>
            <div class="signature-line">
                {{ $seller->name }}<br>
                Date: ____________________
            </div>
        </div>
    </div>
    
    <div class="column">
        <div class="signature-box">
            <strong>Buyer Signature:</strong>
            <div class="signature-line">
                {{ $dealer->company_name }}<br>
                Date: ____________________
            </div>
        </div>
    </div>
</div>

<p class="small mt-20 text-center">
    This sales contract was generated through AutoScout24 SafeTrade platform.<br>
    For disputes or questions, please contact SafeTrade support at support@autoscout24.com
</p>
@endsection
