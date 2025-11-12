# Daldal - Definition und Konzept

## Was ist ein Daldal?

Ein **Daldal** ist ein deutsches Wort oder eine Wortgruppe, die aus denselben Buchstaben in derselben Reihenfolge besteht, aber auf mindestens zwei verschiedene Arten interpretiert werden kann. Die verschiedenen Interpretationen ergeben unterschiedliche semantische Bedeutungen.

## Charakteristika eines Daldals

1. **Identische Buchstabenfolge**: Die Buchstabensequenz ist vollständig identisch
2. **Mehrfache Interpretationsmöglichkeiten**: Mindestens zwei verschiedene semantische Deutungen sind möglich
3. **Gültige deutsche Wörter**: Alle Interpretationen müssen aus validen deutschen Wörtern bestehen
4. **Unterschiedliche Bedeutung**: Die verschiedenen Interpretationen haben unterschiedliche Bedeutungen

## Typen von Daldals

### 1. Zusammengesetzte Wörter vs. Wortgruppen
Ein einzelnes zusammengesetztes Wort kann alternativ als Gruppe einzelner Wörter gelesen werden:
- **Arbeitsamt** → "Arbeit" + "samt" (Wortgruppe) vs. "Arbeitsamt" (zusammengesetztes Substantiv)
- **beinhalten** → "Bein" + "halten" vs. "beinhalten" (Verb)

### 2. Adjektiv + Verb vs. zusammengesetztes Wort
- **beigesetzt** (Partizip von "beisetzen") vs. "beige" + "setzt" (Adjektiv + Verb)

### 3. Präposition + Substantiv vs. Adverb
- **mitnichten** (Adverb: "keineswegs") vs. "mit" + "Nichten" (Präposition + Substantiv im Plural)

### 4. Bindestrich-Verschiebung
Durch unterschiedliche Positionierung von Bindestrichen ergeben sich verschiedene Wortinterpretationen:
- **Abt-Reibung** (Reibung eines Abts) vs. **Abtreibung** (medizinischer/ethischer Begriff)

## Linguistische Eigenschaften

- **Homographie**: Daldals sind eine spezielle Form der Homographie
- **Kompositionalität**: Nutzen die Kompositionalität der deutschen Sprache (Fähigkeit zur Wortbildung durch Zusammensetzung)
- **Ambiguität**: Erzeugen intentional semantische Mehrdeutigkeit
- **Orthographische Identität**: Bei identischer Schreibweise (ohne Leerzeichen/Bindestriche) ununterscheidbar

## Abgrenzung zu anderen sprachlichen Phänomenen

### Nicht-Daldals:
- **Homonyme** (gleiche Schreibweise, aber komplett unterschiedliche Wörter ohne Beziehung): "Bank" (Sitzgelegenheit vs. Geldinstitut)
- **Palindrome**: Wörter die vorwärts und rückwärts gleich gelesen werden
- **Anagramme**: Wörter aus denselben Buchstaben, aber in unterschiedlicher Reihenfolge

## Verwendung

Daldals werden häufig verwendet für:
- Wortspiele und Rätsel
- Humoristische Effekte
- Linguistische Studien zur deutschen Wortbildung
- Quizfragen und Sprachspiele

## Technische Definition (für Programmierung)

Ein Daldal ist eine Zeichenkette S, für die gilt:
```
∃ Zerlegungen Z₁, Z₂ von S in gültige deutsche Wörter:
  Z₁ ≠ Z₂ ∧ concat(Z₁) = concat(Z₂) = S ∧ semantik(Z₁) ≠ semantik(Z₂)
```

Wobei:
- `Zerlegungen` = Möglichkeiten, S in Teilwörter zu zerlegen
- `gültige deutsche Wörter` = Wörter aus einem deutschen Wörterbuch
- `concat` = Konkatenation ohne Trennzeichen
- `semantik` = semantische Bedeutung der Zerlegung
