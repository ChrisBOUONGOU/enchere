import sys

# Utiliser des arguments de ligne de commande (optionnel)
if len(sys.argv) > 1:
    print(f"Argument reçu : {sys.argv[1]}")
else:
    print("Aucun argument reçu")

print("Bonjour depuis Python!")