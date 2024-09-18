SELECT p.name as produits ,p.prices, i.name as image, c.name as categorie ,pc.categorie_id,pc.produits_id
FROM produits as p
INNER JOIN images as i
INNER JOIN categorie as c
INNER JOIN produits_categorie as pc 
WHERE p.id = i.product_id 


SELECT*
FROM produits as p
