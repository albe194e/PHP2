<?php

class Invoice {
    public int $InvoiceId;
    public int $CustomerId;
    public string $InvoiceDate;
    public ?string $BillingAddress;
    public ?string $BillingCity;
    public ?string $BillingState;
    public ?string $BillingCountry;
    public ?string $BillingPostalCode;
    public float $Total;
}