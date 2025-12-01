# 💻 FanZone Back-End

## 📜 Description du Projet

[cite_start]Le back-end de FanZone est le cœur de la solution technique visant à **digitaliser l’accès aux zones spectateurs** pour la CAN 2025[cite: 236, 237]. Il gère toute la logique métier, la gestion des données (zones, matchs, billets, utilisateurs) et l'intégration des services tiers (paiement, email, génération de documents).

[cite_start]L'objectif principal est d'offrir une plateforme fiable et sécurisée pour la réservation de billets et le **contrôle d'accès rapide et fiable** sur site[cite: 193, 209, 253].

---

## ✨ Fonctionnalités Clés du Back-End

[cite_start]Le serveur est responsable des fonctionnalités critiques suivantes de la plateforme[cite: 194, 210, 255]:

### 1. Gestion des Entités

-   [cite_start]**Gestion des Zones & Matchs** : API pour créer, lire, mettre à jour et supprimer les informations relatives aux événements et aux capacités des zones[cite: 194, 210, 256].
-   [cite_start]**Gestion des Utilisateurs & Rôles** : Mise en place de rôles stricts (**Admin, Agent, Fan**) avec des permissions claires pour sécuriser les accès aux fonctionnalités[cite: 195, 211, 277, 278].

### 2. Réservation & Paiement

-   [cite_start]**Checkout Stripe Sécurisé** : Intégration complète de **Stripe Checkout** pour des transactions 100% sécurisées[cite: 192, 194, 208, 210, 258].
-   [cite_start]**Gestion des Webhooks Stripe** : Traitement asynchrone des événements de paiement pour valider la réservation et mettre à jour le statut du billet[cite: 194, 210, 271].
-   [cite_start]**Validation API** : Contrôle strict des accès et des données, notamment lors de la création d'une réservation[cite: 195, 211, 279, 280].

### 3. Billetterie Numérique

-   [cite_start]**Génération QR Code & PDF** : Création automatique et sécurisée des billets sous forme de PDF contenant un QR Code unique pour chaque réservation[cite: 192, 194, 208, 210, 259].
-   [cite_start]**Envoi Email Immédiat** : Envoi immédiat des billets numériques au fan après confirmation de paiement[cite: 194, 210, 260].

### 4. Contrôle d'Accès

-   [cite_start]**Scan & Validation en Temps Réel** : API sécurisée permettant aux agents de scanner le QR Code et d'obtenir une **validation dynamique et unique** du ticket en temps réel pour prévenir la fraude[cite: 194, 196, 210, 212, 261, 284].

### 5. Administration

-   [cite_start]**Reporting Admin** : Back-office complet pour la gestion et la supervision de l'application et du business[cite: 193, 209, 254].

---

## 🛠️ Technologies Utilisées

[cite_start]Le back-end est construit sur une architecture **robuste et éprouvée**[cite: 194, 210, 264].

| Catégorie            | Technologie                                                 | Rôle/Description                                                                                                          |
| :------------------- | :---------------------------------------------------------- | :------------------------------------------------------------------------------------------------------------------------ |
| **Framework**        | [cite_start]**Laravel** (PHP) [cite: 194, 210, 266]         | Framework MVC pour le développement rapide de l'API RESTful.                                                              |
| **Base de Données**  | [cite_start]**MySQL** [cite: 194, 210, 266]                 | Système de gestion de base de données relationnelle pour la persistance des données (réservations, utilisateurs, zones).  |
| **Paiement**         | [cite_start]**Stripe** [cite: 194, 210, 271]                | Solution de paiement intégrée pour le _checkout_ et la gestion sécurisée des transactions (via Webhooks).                 |
| **Authentification** | [cite_start]**Laravel Sanctum** [cite: 195, 211, 275]       | Sécurisation des sessions et des API via des tokens d'authentification pour les rôles _Fan_, _Agent_ et _Admin_.          |
| **Génération Doc.**  | [cite_start]**Imagick** / **SVG Fallback** [cite: 199, 215] | Utilisé pour la génération de fichiers PDF et QR codes (avec un mécanisme de secours en SVG pour garantir la génération). |

---

## 💡 Défis Techniques Relevés

| Problème                                                        | Solution Back-End                                                                                                           |
| :-------------------------------------------------------------- | :-------------------------------------------------------------------------------------------------------------------------- |
| [cite_start]**Problèmes JSON avec Stripe** [cite: 197, 213]     | [cite_start]Normalisation et validation strictes des données reçues via les webhooks[cite: 197, 213].                       |
| [cite_start]**Gestion des métadonnées Stripe** [cite: 198, 214] | [cite_start]Structuration claire des métadonnées pour faire le lien avec la réservation en base de données[cite: 198, 214]. |
| [cite_start]**Tests fonctionnels** [cite: 200, 216]             | [cite_start]Création de scénarios utilisateurs simples et complets pour garantir la stabilité de l'API[cite: 200, 216].     |

---
