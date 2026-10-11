# outbox-flow

Reliable event delivery between services, built on the **Transactional Outbox** pattern.

`outbox-flow` lets a service publish an event or command **as part of its own database transaction** and then takes over the responsibility of delivering it to every interested consumer. If the business change is committed, the message is guaranteed to be delivered. If the transaction is rolled back, the message never exists.

> **Status:** work in progress. The scope and behavior described here are the intended design and may change. This is not a release.

---

## The problem

A service often has to change its own state and notify other services about it. These are two different systems, a database and a message broker or another service's API, so they cannot share one transaction. Whatever order you pick, something can go wrong:

- The data is saved, but the notification is never sent, so other services never learn about the change.
- The notification is sent, but the data is rolled back, so other services react to something that never happened.
- The service crashes between the two steps, leaving the system in an inconsistent state that is hard to notice and harder to repair.

This is known as the dual-write problem. Retrying in application code does not solve it, because the process that would retry may be the one that just crashed.

## The idea

Instead of sending the message directly, the service stores it in a dedicated **outbox table** in its own database, in the same transaction as the business change. Storing a row is as reliable as the database itself. A separate component then reads the outbox and delivers messages to their destinations, retrying until they are acknowledged.

The unreliable part (network, broker, other services) is moved out of the business transaction. The only thing that must succeed atomically is one database commit.

---

## What outbox-flow consists of[composer.json](composer.json)

### Core service

The central part of the system. It is responsible for everything that happens after a message is stored:

- discovering new messages in client outboxes;
- deciding which subscribers should receive each message;
- delivering messages and tracking whether each delivery succeeded;
- retrying failed deliveries and giving up in a controlled way;
- exposing the state of every message, so nothing is silently lost.

### Client library

A library that is added to the service that produces messages. Its goal is to make the pattern easy to adopt without writing the plumbing by hand:

- **Automatic setup.** On startup it creates the outbox table in the client's database and keeps its schema up to date, so no manual migrations are needed.
- **Publish API.** A small, simple interface for putting a message into the outbox. It participates in the transaction that is already open in the client application, which is what makes the guarantee work.
- **No broker knowledge.** The client does not need to know where messages end up, which broker is used, or who is subscribed. It only needs to hand the message over.

### Subscribers

Other services that declare interest in certain kinds of messages. They receive everything they subscribed to, without the producer knowing about them or depending on them.

---

## Expected functionality

### Publishing

- A service publishes a message from inside its business transaction.
- A message carries a **topic** (where it belongs), an **event name** (what happened), a **payload** (the data) and a **correlation ID** (to link related messages and trace a flow across services).
- A message is either an **event** (a fact that already happened, any number of subscribers may react) or a **command** (a request for something to be done).
- The message is saved only if the transaction commits and disappears if it rolls back.

### Message lifecycle

Every message moves through a clear set of states:

1. **Pending**: stored and waiting to be delivered.
2. **Processing**: currently being delivered, so it will not be picked up twice at the same moment.
3. **Sent**: delivery was acknowledged by the subscriber.
4. **Failed**: delivery did not succeed and no attempts are left.

A failed attempt does not end the story. Each message has a limited number of delivery attempts. After a failure the message returns to pending and the attempt counter goes down. Only when the attempts run out does it become failed.

### Delivery

- Messages are discovered in client outboxes and routed to every subscriber of the matching topic and event.
- Delivery is **at-least-once**: a message may occasionally arrive more than once (for example when an acknowledgement gets lost), but it will not be lost.
- Messages of the same logical entity are delivered **in order**. There is no global ordering across unrelated messages, which keeps the system scalable.
- Failed deliveries are retried, with growing delays between attempts so a struggling subscriber is not overloaded.
- Several instances of the delivery component can run in parallel without sending the same message twice at the same moment.

### Idempotency

Because delivery is at-least-once, every message has a unique ID that subscribers can use to detect duplicates and safely ignore repeats.

### Failure handling

- A subscriber being down does not affect the producer. Messages wait in the outbox and are delivered when the subscriber recovers.
- The core service being down does not affect the producer either. Messages are already safely stored and are picked up after a restart.
- Messages that exhaust their attempts stay visible as failed, so they can be inspected, fixed and re-sent instead of vanishing.

### Operations and visibility

- Clear status for every message, so you can answer "was it delivered, to whom, and when?".
- Counters for pending, processing, sent and failed messages, delivery latency and retry rates.
- Cleanup of old delivered messages so outbox tables do not grow forever.

---

## Where it is useful

**Microservices that must stay consistent.** When an order service creates an order, the billing, inventory and notification services must all hear about it, even if one of them is temporarily down.

**Integrating with external systems.** Sending data to payment providers, CRMs, ERPs or partner APIs, where a lost or duplicated call has real cost and retries must be controlled.

**Notifications.** Emails, SMS and push notifications that must be sent exactly because a specific change happened, and never for a change that was rolled back.

**Event-driven architectures.** Publishing domain events to a broker without risking the database and the broker disagreeing about what happened.

**Search and cache synchronization.** Keeping a search index, cache or read model in step with the main database without losing updates.

**Data replication and analytics.** Feeding data warehouses or other services with a reliable stream of changes.

**Workflows and sagas.** Long-running processes spanning several services, where each step triggers the next through a command that must not be lost.

**Legacy modernization.** Adding reliable event publishing to an existing application with minimal changes to its business code.

---

## Why not publish to a broker directly?

Publishing inside a business method looks simple, but the call to the broker is not covered by the database transaction. Any crash, timeout or network error between the two steps produces an inconsistency. Writing the message to the database first removes that window entirely.

## Why not just use change data capture?

Reading the database log is a valid approach, but it needs access to database internals and extra infrastructure. `outbox-flow` aims to work with a plain table and a small client library, so it can be adopted by a typical application without special database setup.

---

## Roadmap

- [ ] Core domain models
- [ ] Outbox table setup in the client library
- [ ] Publish API in the client library
- [ ] Reading and routing of messages
- [ ] Subscription management
- [ ] Delivery with retries and backoff
- [ ] Handling of permanently failed messages
- [ ] Metrics and observability
- [ ] Documentation and examples

## License

Licensed under the [Apache License 2.0](LICENSE).