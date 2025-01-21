# Restaurant Management System - Entity Documentation

## Overview

The Order entity represents a customer's complete dining transaction within the restaurant system. It serves as the central entity that connects customer requests, kitchen operations, and payment processing.

## Entity Details

### Core Fields

| Field       | Type   | Required | Description                                             |
| ----------- | ------ | -------- | ------------------------------------------------------- |
| id          | UUID   | Yes      | Unique identifier for the order                         |
| orderNumber | String | Yes      | Human-readable order number (e.g., "ORD-2024-001")      |
| branchId    | UUID   | Yes      | Reference to the branch where order was placed          |
| tableId     | UUID   | No       | Reference to the table (null for takeaway/delivery)     |
| employeeId  | UUID   | Yes      | Reference to the employee who created/handles the order |
| status      | Enum   | Yes      | Current status of the order                             |
| type        | Enum   | Yes      | Type of order (dine-in, takeaway, delivery)             |

### Temporal Fields

| Field       | Type     | Required | Description                  |
| ----------- | -------- | -------- | ---------------------------- |
| createdAt   | DateTime | Yes      | When the order was created   |
| updatedAt   | DateTime | Yes      | Last modification timestamp  |
| completedAt | DateTime | No       | When the order was completed |

### Financial Fields

| Field         | Type    | Required | Description                           |
| ------------- | ------- | -------- | ------------------------------------- |
| subtotal      | Decimal | Yes      | Sum of all items before tax/discounts |
| tax           | Decimal | Yes      | Tax amount                            |
| discount      | Decimal | No       | Discount amount (if any)              |
| total         | Decimal | Yes      | Final total amount                    |
| paymentStatus | Enum    | Yes      | Current payment status                |

### Customer Information

| Field           | Type   | Required | Description                      |
| --------------- | ------ | -------- | -------------------------------- |
| customerName    | String | No       | Customer name for reference      |
| customerContact | String | No       | Customer contact information     |
| notes           | String | No       | General notes about the order    |
| specialRequests | String | No       | Special instructions or requests |

## Enums

### OrderStatus

```typescript
enum OrderStatus {
    PENDING, // Just created
    IN_PROGRESS, // Being prepared
    READY, // Ready for service/pickup
    SERVED, // Delivered to customer
    COMPLETED, // Paid and finished
    CANCELLED, // Cancelled order
}
```

### OrderType

```typescript
enum OrderType {
    DINE_IN, // Restaurant table service
    TAKEAWAY, // Pick up by customer
    DELIVERY, // Delivery service
}
```

### PaymentStatus

```typescript
enum PaymentStatus {
    UNPAID, // No payment received
    PARTIALLY_PAID, // Some payments received
    PAID, // Fully paid
}
```

<!-- ## Related Entities

### OrderItem

-   Represents individual items in the order
-   Contains quantity, price at time of order, and item-specific notes
-   Links to Product and optional ProductCombination

### OrderPayment

-   Handles multiple payments for a single order
-   Tracks payment method, amount, and status
-   Supports split payments across different methods

## Examples

### Basic Dine-in Order

```json
{
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "orderNumber": "ORD-2024-001",
    "branchId": "b001",
    "tableId": "t007",
    "employeeId": "e123",
    "status": "PENDING",
    "type": "DINE_IN",
    "subtotal": 23.08,
    "tax": 2.31,
    "total": 25.39,
    "paymentStatus": "UNPAID"
}
```

### Split Payment Example

```json
{
    "order": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "total": 25.39,
        "paymentStatus": "PARTIALLY_PAID"
    },
    "payments": [
        {
            "id": "p001",
            "amount": 15.0,
            "method": "CASH"
        },
        {
            "id": "p002",
            "amount": 10.39,
            "method": "CREDIT_CARD"
        }
    ]
}
```

## Validation Rules

1. **Order Creation**

    - Must have at least one item
    - Must have valid branch and employee references
    - Table required for DINE_IN orders

2. **Financial Validation**

    - Total must equal subtotal + tax - discount
    - All payments must sum to total for PAID status
    - Individual payments cannot exceed total

3. **Status Transitions**

    - Must follow valid sequence (e.g., cannot go from PENDING to COMPLETED)
    - Cannot modify COMPLETED or CANCELLED orders
    - Must be SERVED before COMPLETED

4. **Timestamps**
    - completedAt must be after createdAt
    - updatedAt must be >= createdAt

## Common Operations

### Status Updates

```typescript
function updateOrderStatus(orderId: string, newStatus: OrderStatus): void {
    // Validate status transition
    // Update status
    // Update updatedAt timestamp
    // Trigger relevant notifications
}
```

### Payment Processing

```typescript
function processPayment(
    orderId: string,
    amount: number,
    method: PaymentMethod
): void {
    // Validate payment amount
    // Create payment record
    // Update order payment status
    // Handle split payment logic
}
```

## Notes

-   Orders should be immutable after reaching COMPLETED status
-   Support partial payments but validate total payments against order total
-   Maintain audit trail of status changes and modifications
-   Consider timezone handling for international branches
-   Implement robust error handling for payment processing

## OrderItem Entity

### Core Fields

| Field         | Type    | Required | Description                                    |
| ------------- | ------- | -------- | ---------------------------------------------- |
| id            | UUID    | Yes      | Unique identifier for the order item           |
| orderId       | UUID    | Yes      | Reference to the parent order                  |
| productId     | UUID    | Yes      | Reference to the product                       |
| combinationId | UUID    | No       | Reference to product combination if applicable |
| quantity      | Integer | Yes      | Number of items ordered                        |
| priceAtTime   | Decimal | Yes      | Price of item when ordered                     |
| status        | Enum    | Yes      | Current status of the item                     |
| notes         | String  | No       | Special instructions for this item             |

### OrderItemStatus

```typescript
enum OrderItemStatus {
    PENDING, // Just ordered
    PREPARING, // Being prepared in kitchen
    READY, // Ready for service
    SERVED, // Delivered to customer
    CANCELLED, // Cancelled item
}
```

## OrderPayment Entity

### Core Fields

| Field         | Type     | Required | Description                       |
| ------------- | -------- | -------- | --------------------------------- |
| id            | UUID     | Yes      | Unique identifier for the payment |
| orderId       | UUID     | Yes      | Reference to the order            |
| amount        | Decimal  | Yes      | Payment amount                    |
| method        | Enum     | Yes      | Payment method used               |
| transactionId | String   | No       | External payment system reference |
| timestamp     | DateTime | Yes      | When payment was processed        |
| status        | Enum     | Yes      | Current payment status            |

### PaymentMethod

```typescript
enum PaymentMethod {
    CASH,
    CREDIT_CARD,
    DEBIT_CARD,
    DIGITAL_WALLET,
    GIFT_CARD,
}
```

### PaymentStatus

```typescript
enum PaymentStatus {
    PENDING, // Payment initiated
    COMPLETED, // Payment successful
    FAILED, // Payment failed
    REFUNDED, // Payment refunded
}
```

## Product Entity

### Core Fields

| Field           | Type    | Required | Description                           |
| --------------- | ------- | -------- | ------------------------------------- |
| id              | UUID    | Yes      | Unique identifier for the product     |
| name            | String  | Yes      | Product name                          |
| description     | String  | No       | Product description                   |
| basePrice       | Decimal | Yes      | Base price before variants            |
| category        | String  | Yes      | Product category                      |
| isAvailable     | Boolean | Yes      | Whether product can be ordered        |
| preparationTime | Integer | No       | Estimated preparation time in minutes |
| imageUrl        | String  | No       | Product image reference               |

## ProductVariant Entity

### Core Fields

| Field         | Type    | Required | Description                       |
| ------------- | ------- | -------- | --------------------------------- |
| id            | UUID    | Yes      | Unique identifier for the variant |
| productId     | UUID    | Yes      | Reference to base product         |
| name          | String  | Yes      | Variant name                      |
| priceModifier | Decimal | Yes      | Price adjustment (+/-)            |
| isAvailable   | Boolean | Yes      | Whether variant can be ordered    |

## ProductCombination Entity

### Core Fields

| Field       | Type    | Required | Description                           |
| ----------- | ------- | -------- | ------------------------------------- |
| id          | UUID    | Yes      | Unique identifier for the combination |
| productId   | UUID    | Yes      | Reference to base product             |
| variantIds  | UUID[]  | Yes      | Array of included variant IDs         |
| finalPrice  | Decimal | Yes      | Final price for combination           |
| isAvailable | Boolean | Yes      | Whether combination can be ordered    |

## Branch Entity

### Core Fields

| Field       | Type    | Required | Description                      |
| ----------- | ------- | -------- | -------------------------------- |
| id          | UUID    | Yes      | Unique identifier for the branch |
| name        | String  | Yes      | Branch name                      |
| address     | String  | Yes      | Physical location                |
| phoneNumber | String  | Yes      | Contact number                   |
| taxRate     | Decimal | Yes      | Local tax rate                   |
| isActive    | Boolean | Yes      | Whether branch is operational    |

## Table Entity

### Core Fields

| Field    | Type    | Required | Description                        |
| -------- | ------- | -------- | ---------------------------------- |
| id       | UUID    | Yes      | Unique identifier for the table    |
| branchId | UUID    | Yes      | Reference to branch                |
| number   | String  | Yes      | Table number/identifier            |
| capacity | Integer | Yes      | Number of seats                    |
| status   | Enum    | Yes      | Current table status               |
| location | String  | No       | Location description within branch |

### TableStatus

```typescript
enum TableStatus {
    AVAILABLE,
    OCCUPIED,
    RESERVED,
    MAINTENANCE,
}
```

## Employee Entity

### Core Fields

| Field    | Type    | Required | Description                        |
| -------- | ------- | -------- | ---------------------------------- |
| id       | UUID    | Yes      | Unique identifier for the employee |
| branchId | UUID    | Yes      | Reference to branch                |
| name     | String  | Yes      | Employee name                      |
| role     | Enum    | Yes      | Employee role                      |
| isActive | Boolean | Yes      | Whether employee is active         |
| pin      | String  | Yes      | Authentication PIN                 |

### EmployeeRole

```typescript
enum EmployeeRole {
    WAITER,
    KITCHEN_STAFF,
    CASHIER,
    MANAGER,
    ADMIN,
}
``` -->
