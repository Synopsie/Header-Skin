# Arkania SkinSave Library

La bibliothèque `SkinSave` est une solution PHP conçue pour faciliter la manipulation et la sauvegarde des skins de personnages pour Minecraft. Elle permet de valider, redimensionner et sauvegarder les données de skin sous forme d'images.

## Fonctionnalités

- Validation de la taille des skins.
- Conversion des données de skin en images.
- Redimensionnement des images de skin.
- Sauvegarde des têtes de personnages à partir des données de skin.

## Installation

Vous mettez le fichier `SkinSave.php` dans votre projet et pensez à changer le namespace si besoin.

## Utilisation

### Valider la taille d'un skin

Pour valider la taille d'un skin, utilisez la méthode `validateSize` :

```php
$isValid = \arkania\SkinSave::validateSize($size);
```

### Enregistrer un skin en config.

Pour enregistrer un skin en config, utilisez la méthode `skinDataToImage` :

```php
$image = \arkania\SkinSave::skinDataToImage($skinData);
```

### Redimensionner une image de skin

Pour redimensionner une image de skin, utilisez la méthode `resize_image` :

```php
imagepng(SkinSave::skinDataToImage($player->getSkin()->getSkinData()), Main::getInstance()->getDataFolder() . "skins/" . $player->getName() . ".png");
```

### Sauvegarder la tête d'un personnage

Pour sauvegarder la tête d'un personnage à partir des données de skin, utilisez la méthode `savePlayerHead` :

```php
\arkania\SkinSave::savePlayerHead($playerName, $skinData, $path);
```

## Contribution

Les contributions à la bibliothèque sont les bienvenues. Veuillez suivre les conventions de code standard PHP et soumettre vos pull requests pour examen.

## Licence

Cette bibliothèque est distribuée sous la licence MIT. Voir le fichier `LICENSE` pour plus d'informations.

![Header-Skin](header-skin.png)