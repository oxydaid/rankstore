# MAYAR ID - PAYMENT PROVIDER

---

Endpoint Production:

```
https://api.mayar.id/hl/v1/
```

Endpoint Sandbox:

```
https://api.mayar.club/hl/

```

---

## Create Payment

Endpoint: `POST`

```
payment/create
```

Contoh Request:

```
curl --request POST 'https://api.mayar.id/hl/v1/invoice/create' \
--header 'Authorization: Bearer Paste-Your-API-Key-Here' \
--data-raw '{
        "name": "Azumii",
        "email": "azumiikecee@gmail.com",
        "amount": 170000,
        "mobile": "08996136751",
        "redirectUrl": "https://web.mayar.id/",
        "description": "Testing ReqPayment",
        "expiredAt": "2025-12-29T09:41:09.401Z"
    }
'
```

Contoh response:

```
{
    "statusCode": 200,
    "messages": "success",
    "data": {
        "id": "e890d24a-cfc0-4915-83d2-3166b9ffba9e",
        "transaction_id": "040d5adb-1496-45de-8435-5cab16526a8c",
        "transactionId": "040d5adb-1496-45de-8435-5cab16526a8c",
        "link": "https://andiak.myr.id/invoices/ohsjrd3wko"
    }
}
```

---

## Cek Status Pembayaran

Endpoint: `GET`

```
payment/{id}
```

Contoh parameter:

```
e890d24a-cfc0-4915-83d2-3166b9ffba9e
```

Contoh response:

```
{
    "statusCode": 200,
    "messages": "success",
    "data": {
        "id": "e890d24a-cfc0-4915-83d2-3166b9ffba9e",
        "link": "ohsjrd3wko",
        "name": "Azumii",
        "category": null,
        "limit": null,
        "type": "payment_request",
        "userId": "348e083d-315a-4e5c-96b1-5a2a98c48413",
        "event": null,
        "order": null,
        "qty": null,
        "amount": 100000,
        "status": "unpaid",
        "description": "Ubah ReqPayment",
        "coverImageId": null,
        "multipleImageId": null,
        "coverImage": null,
        "multipleImage": null
    }
}
```

---

## Validasi Lisensi Software

Endpoint: `POST`

```
license/verify
```

Contoh request:

```
curl --request POST 'https://api.mayar.id/software/v1/license/verify' \
--header 'Authorization: Bearer Paste-Your-API-Key-Here' \
--data '{
    "licenseCode": "YOUR-LICENSE-CODE",
    "productId": "YOUR-PRODUCT-ID"
}'
```

Contoh response:

```
{
    "statusCode": 200,
    "isLicenseActive": true,
    "licenseCode": {
        "licenseCode": "LICENSECODE12345",
        "status": "ACTIVE",
        "expiredAt": "2025-12-12T19:46:24.000Z",
        "transactionId": "994d4071-a81e-4558-a854-47530eea9b6d",
        "productId": "84d1d247-a8b3-4c7d-96f0-cf276edb7c33",
        "customerId": "6a38cf26-6bab-42c8-92be-72f3a9fd4c33",
        "customerName": "John Doe",
        "customerEmail": "johndoe@gmail.com",
        "activationLimit": "Tidak terbatas",
        "useCount": 10,
        "createdAt": "2024-02-12T08:41:13.579Z",
        "updatedAt": "2024-02-12T08:57:36.047Z"
    }
}
```

---

## SaaS

Endpoint: `POST`

```
license/activate
license/verify
license/deactivate
```

Contoh Request:

```
curl --request POST 'https://api.mayar.id/saas/v1/license/..' \
--header 'Authorization: Bearer Paste-Your-API-Key-Here' \
--data '{
    "licenseCode": "YOUR-LICENSE-CODE",
    "productId": "YOUR-PRODUCT-ID"
}'
```

contoh response: `verify`

```
{
    "statusCode": 200,
    "isLicenseActive": true,
    "licenseCode": {
        "licenseCode": "LICENSECODE12345",
        "status": "ACTIVE",
        "expiredAt": "2025-12-12T19:46:24.000Z",
        "transactionId": "994d4071-a81e-4558-a854-47530eea9b6d",
        "productId": "84d1d247-a8b3-4c7d-96f0-cf276edb7c33",
        "customerId": "6a38cf26-6bab-42c8-92be-72f3a9fd4c33",
        "customerName": "John Doe",
        "customerEmail": "johndoe@gmail.com",
        "activationLimit": "Tidak terbatas",
        "useCount": 10,
        "createdAt": "2024-02-12T08:41:13.579Z",
        "updatedAt": "2024-02-12T08:57:36.047Z",
        "membershipTierId": "c96850f9-b379-4ed3-bcf2-6d88a87bd20c",
        "membershipTierName": "Master Black Belt Membership"
    }
}
```

Contoh response: `Activate / Deactivate`

```
{
    "statusCode": 200,
    "message": "Success updating license code status to ACTIVE/INACTIVE."
}
```

---

## Webhook

Payload yang dikirim (event payment):

```
{
  "event": "payment.received",
  "data": {
    "id": "9356ec92-32ae-4d99-a1a7-51b11dff4d84",
    "transactionId": "9356ec92-32ae-4d99-a1a7-51b11dff4d84",
    "status": "SUCCESS",
    "transactionStatus": "created",
    "createdAt": 1693817623264,
    "updatedAt": 1693817626638,
    "merchantId": "348e083d-315a-4e5c-96b1-5a2a98c48413",
    "merchantName": "Malo Gusto",
    "merchantEmail": "aldodwier@gmail.com",
    "customerName": "Student Test",
    "customerEmail": "student@student.com",
    "customerMobile": "0815",
    "amount": 1029,
    "isAdminFeeBorneByCustomer": true,
    "isChannelFeeBorneByCustomer": true,
    "productId": "e2b3f5d5-0c62-47ba-8a01-6c1c209e0f77",
    "productName": "Kelas Pemrograman Web Dasar",
    "productType": "course",
    "pixelFbp": "fb.1.1693462870069.763035785",
    "pixelFbc": null,
  }
}
```

atau

```
{
  "event": "testing",
  "data": {
    "id": "123456789",
    "status": "SUCCESS",
    "createdAt": 1701310589229,
    "updatedAt": 1701310589229,
    "merchantId": "12345",
    "merchantName": "Merchant Demo",
    "merchantEmail": "example.merchant@myr.id",
    "customerName": "Customer Demo",
    "customerEmail": "example.customer@myr.id",
    "customerMobile": "0123456789",
    "amount": 100000,
    "isAdminFeeBorneByCustomer": true,
    "isChannelFeeBorneByCustomer": true,
    "productId": "11223344",
    "productName": "Product Example",
    "productType": "digital_product",
    "custom_field": [
      {
        "name": "Label Input",
        "description": "Input description",
        "fieldType": "text",
        "isRequired": false,
        "key": "0011223344",
        "type": "string",
        "value": "Value Input"
      }
    ]
  }
}
```

---

# Catatan

* Catat "id" dari response **create payment** simpan ke dalam database transaksi pada aplikasi karena id tersebut lah sebagai tanda pengenal(referensi) antara aplikasi dan mayar (sebagai parameter untuk cek status/detail payment)
* berbeda dengan cek status, id webhook tidak sama dengan id pada cek status dan id(referensi) pada database aplikasi. namun ada field "productId" yang mana field ini valuenya sama dengan id dari response create paymnet dan response cek detail payment
