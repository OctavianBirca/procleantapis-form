// JavaScript simplu pentru taburi
document.addEventListener('DOMContentLoaded', function() {
	try { console.log('[PCDF] JS loaded, tab buttons:', document.querySelectorAll('.tab-button').length); } catch(e){}
	// Variabile pentru calcul
	let selectedCarpet = null;
	let currentSurface = 6.0;
	let steamCleanerSelected = false;
	let deliveryCost = 0;
	let selectedDeliveryType = '';

	// Variabile pentru textile
	let textileDeliveryCost = 0;
	let selectedTextileDeliveryType = '';

	// Variabile pentru literie
	let literieDeliveryCost = 0;
	let selectedLiterieDeliveryType = '';

	// Selectează toate butoanele tab
	const tabButtons = document.querySelectorAll('.tab-button');
	const tabContents = document.querySelectorAll('.tab-content');

	// Adaugă event listener pentru fiecare buton tab
	tabButtons.forEach(button => {
		button.addEventListener('click', function() {
			const targetTab = this.getAttribute('data-tab');

			// Elimină clasa active de la toate butoanele
			tabButtons.forEach(btn => btn.classList.remove('active'));
            
			// Elimină clasa active de la toate conținuturile
			tabContents.forEach(content => content.classList.remove('active'));

			// Adaugă clasa active la butonul apăsat
			this.classList.add('active');

			// Adaugă clasa active la conținutul corespunzător
			const targetContent = document.getElementById(targetTab);
			if (targetContent) {
				targetContent.classList.add('active');
			}
		});
	});

	// Funcționalitate pentru selecția covoarele
	const carpetCards = document.querySelectorAll('.carpet-card');
    
	carpetCards.forEach(card => {
		card.addEventListener('click', function() {
			// Elimină selecția de la toate cardurile
			carpetCards.forEach(c => c.classList.remove('selected'));
            
			// Adaugă selecția la cardul apăsat
			this.classList.add('selected');
            
			// Stochează informațiile despre covor
			selectedCarpet = {
				name: this.getAttribute('data-name'),
				price: parseFloat(this.getAttribute('data-price'))
			};
            
			updatePriceCalculation();
		});
	});

	// Funcționalitate pentru dimensiuni
	const largeurRange = document.getElementById('largeur-range');
	const largeurInput = document.getElementById('largeur-input');
	const longueurRange = document.getElementById('longueur-range');
	const longueurInput = document.getElementById('longueur-input');
    
	if (largeurRange && largeurInput) {
		largeurRange.addEventListener('input', function() {
			largeurInput.value = this.value;
			updateSurface();
		});
        
		largeurInput.addEventListener('input', function() {
			largeurRange.value = this.value;
			updateSurface();
		});
	}
    
	if (longueurRange && longueurInput) {
		longueurRange.addEventListener('input', function() {
			longueurInput.value = this.value;
			updateSurface();
		});
        
		longueurInput.addEventListener('input', function() {
			longueurRange.value = this.value;
			updateSurface();
		});
	}

	// Funcția pentru calculul suprafeței
	function updateSurface() {
		const largeur = parseFloat(largeurInput?.value || 2);
		const longueur = parseFloat(longueurInput?.value || 3);
		currentSurface = largeur * longueur;
        
		const surfaceElement = document.getElementById('surface-value');
		if (surfaceElement) {
			surfaceElement.textContent = `${currentSurface.toFixed(1)} m²`;
		}
        
		updatePriceCalculation();
	}

	// Funcționalitate pentru opțiuni (nettoyeur à vapeur)
	const steamCleaner = document.getElementById('steam-cleaner');
	if (steamCleaner) {
		steamCleaner.addEventListener('change', function() {
			steamCleanerSelected = this.checked;
			updatePriceCalculation();
		});
	}

	// Funcționalitate pentru opțiunile de livrare
	const deliveryOptions = document.querySelectorAll('input[name="delivery"]');
	deliveryOptions.forEach(option => {
		option.addEventListener('change', function() {
			deliveryCost = parseFloat(this.getAttribute('data-cost'));
			selectedDeliveryType = this.value;
			updatePriceCalculation();
		});
	});

	// Funcția principală pentru calculul prețului
	function updatePriceCalculation() {
		// Calculează prețul materialului
		const materialCost = selectedCarpet ? (selectedCarpet.price * currentSurface) : 0;
        
		// Calculează costul nettoyeur
		const steamCost = steamCleanerSelected ? 15 : 0;
        
		// Calculează totalul
		const totalCost = materialCost + steamCost + deliveryCost;

		// Actualizează afișarea
		document.getElementById('selected-carpet').textContent = 
			selectedCarpet ? `${selectedCarpet.name} (${selectedCarpet.price}€/m²)` : 'Aucun sélectionné';
        
		document.getElementById('calc-surface').textContent = `${currentSurface.toFixed(1)} m²`;
		document.getElementById('material-cost').textContent = `${materialCost.toFixed(2)} €`;
		document.getElementById('steam-cost').textContent = `${steamCost.toFixed(2)} €`;
        
		// Afișarea costului de livrare cu tratament special pentru "distant"
		if (selectedDeliveryType === 'distant') {
			document.getElementById('delivery-cost').textContent = `à partir de ${deliveryCost.toFixed(0)} €`;
		} else {
			document.getElementById('delivery-cost').textContent = `${deliveryCost.toFixed(2)} €`;
		}
        
		document.getElementById('total-price').textContent = `${totalCost.toFixed(2)} €`;
	}

	// Inițializare
	updateSurface();
	updatePriceCalculation();

	// Funcționalitate pentru textile - controale cantitate
	const qtyButtons = document.querySelectorAll('.qty-btn');
	const qtyInputs = document.querySelectorAll('.qty-input');

	qtyButtons.forEach(button => {
		// Nu aplică pentru butoanele din tab-ul literie (care au propriul event listener)
		if (!button.closest('#literie')) {
			button.addEventListener('click', function() {
				const targetId = this.getAttribute('data-target');
				const input = document.getElementById(targetId);
				const isPlus = this.classList.contains('plus');
				const isMinus = this.classList.contains('minus');
                
				let currentValue = parseInt(input.value) || 0;
                
				if (isPlus) {
					currentValue++;
				} else if (isMinus && currentValue > 0) {
					currentValue--;
				}
                
				input.value = currentValue;
                
				// Trigger change event pentru actualizarea calculelor
				input.dispatchEvent(new Event('change'));
			});
		}
	});

	// Event listeners pentru input-urile de cantitate
	qtyInputs.forEach(input => {
		input.addEventListener('change', function() {
			// Asigură că valoarea nu este negativă
			if (parseInt(this.value) < 0) {
				this.value = 0;
			}
            
			// Actualizează calculul pentru textile
			updateTextilePriceCalculation();
		});
	});

	// Funcționalitate pentru checkbox-urile de nettoyeur à vapeur din textile
	const steamCheckboxes = document.querySelectorAll('.steam-checkbox');
	steamCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', function() {
			const isChecked = this.checked;
			const productName = this.id.replace('-steam', '');
			console.log(`Nettoyeur à vapeur pentru ${productName}: ${isChecked ? 'activat' : 'dezactivat'}`);
            
			// Actualizează calculul pentru textile
			updateTextilePriceCalculation();
		});
	});

	// Funcționalitate pentru opțiunile de livrare din textile
	const textileDeliveryOptions = document.querySelectorAll('input[name="textile-delivery"]');
	textileDeliveryOptions.forEach(option => {
		option.addEventListener('change', function() {
			textileDeliveryCost = parseFloat(this.getAttribute('data-cost'));
			selectedTextileDeliveryType = this.value;
			console.log(`Livrare textile selectată: ${selectedTextileDeliveryType} - Cost: ${textileDeliveryCost}€`);
            
			// Actualizează calculul pentru textile
			updateTextilePriceCalculation();
		});
	});

	// Funcția pentru calculul prețului textile
	function updateTextilePriceCalculation() {
		let totalItemsCost = 0;
		let totalSteamCost = 0;
		let selectedItems = [];

		// Calculează costul articolelor și steam cleaning
		const textileCards = document.querySelectorAll('.textile-card');
		textileCards.forEach(card => {
			const productName = card.getAttribute('data-name');
			const productPrice = card.getAttribute('data-price');
			const qtyInputId = card.querySelector('.qty-input').id;
			const steamCheckboxId = card.querySelector('.steam-checkbox').id;
            
			const quantity = parseInt(document.getElementById(qtyInputId).value) || 0;
			const steamSelected = document.getElementById(steamCheckboxId).checked;
            
			if (quantity > 0) {
				if (productPrice === 'devis') {
					selectedItems.push(`${productName} x${quantity} (sur devis)`);
				} else {
					const itemCost = parseFloat(productPrice) * quantity;
					totalItemsCost += itemCost;
					selectedItems.push(`${productName} x${quantity} (${productPrice}€ chacun)`);
				}
                
				if (steamSelected) {
					totalSteamCost += 15 * quantity;
				}
			}
		});

		// Calculează totalul
		const totalCost = totalItemsCost + totalSteamCost + textileDeliveryCost;

		// Actualizează afișarea
		document.getElementById('textile-selected-items').textContent = 
			selectedItems.length > 0 ? selectedItems.join(', ') : 'Aucun sélectionné';
        
		document.getElementById('textile-items-cost').textContent = `${totalItemsCost.toFixed(2)} €`;
		document.getElementById('textile-steam-cost').textContent = `${totalSteamCost.toFixed(2)} €`;
        
		// Afișarea costului de livrare cu tratament special pentru "distant"
		if (selectedTextileDeliveryType === 'distant') {
			document.getElementById('textile-delivery-cost').textContent = `à partir de ${textileDeliveryCost.toFixed(0)} €`;
		} else {
			document.getElementById('textile-delivery-cost').textContent = `${textileDeliveryCost.toFixed(2)} €`;
		}
        
		document.getElementById('textile-total-price').textContent = `${totalCost.toFixed(2)} €`;
	}

	// Funcționalitate pentru opțiunile de livrare din literie
	const literieDeliveryOptions = document.querySelectorAll('input[name="literie-delivery"]');
	literieDeliveryOptions.forEach(option => {
		option.addEventListener('change', function() {
			literieDeliveryCost = parseFloat(this.getAttribute('data-cost'));
			selectedLiterieDeliveryType = this.value;
			console.log(`Livrare literie selectată: ${selectedLiterieDeliveryType} - Cost: ${literieDeliveryCost}€`);
            
			// Actualizează calculul pentru literie
			updateLiteriePriceCalculation();
		});
	});

	// Funcționalitate pentru checkbox-urile de nettoyage vapeur din literie
	const literieStreamCheckboxes = document.querySelectorAll('.literie-steam');
	literieStreamCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', function() {
			updateLiteriePriceCalculation();
		});
	});

	// Funcționalitate pentru controalele de cantitate din literie
	const literieQtyButtons = document.querySelectorAll('.textile-card .qty-btn');
	const literieQtyInputs = document.querySelectorAll('.textile-card .qty-input');

	// Event listeners pentru cantitățile din literie
	literieQtyInputs.forEach(input => {
		// Verifică dacă input-ul aparține tab-ului literie
		if (input.closest('#literie')) {
			input.addEventListener('change', function() {
				if (parseInt(this.value) < 0) {
					this.value = 0;
				}
				updateLiteriePriceCalculation();
			});
		}
	});

	literieQtyButtons.forEach(button => {
		// Verifică dacă butonul aparține tab-ului literie
		if (button.closest('#literie')) {
			button.addEventListener('click', function() {
				const targetId = this.getAttribute('data-target');
				const input = document.getElementById(targetId);
				const isPlus = this.classList.contains('plus');
				const isMinus = this.classList.contains('minus');
                
				let currentValue = parseInt(input.value) || 0;
                
				if (isPlus) {
					// Incrementare cu 1
					currentValue++;
				} else if (isMinus && currentValue > 0) {
					// Decrementare cu 1
					currentValue--;
				}
                
				input.value = currentValue;
				updateLiteriePriceCalculation();
			});
		}
	});

	// Funcția pentru calculul prețului literie
	function updateLiteriePriceCalculation() {
		let totalItemsCost = 0;
		let totalVersoCost = 0;
		let totalSteamCost = 0;
		let selectedItems = [];

		// Calculează costul articolelor, verso și steam pentru fiecare produs
		const literieCards = document.querySelectorAll('#literie .textile-card');
		literieCards.forEach(card => {
			const productName = card.getAttribute('data-name');
			const productPrice = card.getAttribute('data-price');
			const qtyInputId = card.querySelector('.qty-input').id;
            
			const quantity = parseInt(document.getElementById(qtyInputId).value) || 0;
            
			if (quantity > 0) {
				const itemCost = parseFloat(productPrice) * quantity;
				totalItemsCost += itemCost;
				selectedItems.push(`${productName} x${quantity} (${productPrice}€ chacun)`);
                
				// Verifică opțiunea verso pentru acest produs
				const versoCheckbox = card.querySelector('.verso-checkbox');
				if (versoCheckbox && versoCheckbox.checked) {
					totalVersoCost += 15 * quantity;
				}
                
				// Verifică opțiunea steam pentru acest produs
				const steamCheckbox = card.querySelector('.steam-checkbox');
				if (steamCheckbox && steamCheckbox.checked) {
					// Dacă verso este bifat pentru acest produs, steam costă x2
					const versoSelected = versoCheckbox && versoCheckbox.checked;
					const steamCost = versoSelected ? 30 : 15;
					totalSteamCost += steamCost * quantity;
				}
			}
		});

		// Calculează totalul
		const totalCost = totalItemsCost + totalVersoCost + totalSteamCost + literieDeliveryCost;

		// Actualizează afișarea
		document.getElementById('literie-selected-items').textContent = 
			selectedItems.length > 0 ? selectedItems.join(', ') : 'Aucun sélectionné';
        
		document.getElementById('literie-items-cost').textContent = `${totalItemsCost.toFixed(2)} €`;
		document.getElementById('literie-verso-cost').textContent = `${totalVersoCost.toFixed(2)} €`;
        
		// Afișare specială pentru steam cost cu indicare când e dublat
		let steamDisplayText = `${totalSteamCost.toFixed(2)} €`;
		const hasDoubledSteam = document.querySelectorAll('#literie .verso-checkbox:checked').length > 0 && 
							   document.querySelectorAll('#literie .steam-checkbox:checked').length > 0;
		if (hasDoubledSteam) {
			steamDisplayText += ' (inclus ×2 pour verso)';
		}
		document.getElementById('literie-steam-cost').textContent = steamDisplayText;
        
		// Afișarea costului de livrare cu tratament special pentru "distant"
		if (selectedLiterieDeliveryType === 'distant') {
			document.getElementById('literie-delivery-cost').textContent = `à partir de ${literieDeliveryCost.toFixed(0)} €`;
		} else {
			document.getElementById('literie-delivery-cost').textContent = `${literieDeliveryCost.toFixed(2)} €`;
		}
        
		document.getElementById('literie-total-price').textContent = `${totalCost.toFixed(2)} €`;
	}

	// Event listeners pentru checkbox-urile verso din carduri
	const versoCheckboxes = document.querySelectorAll('.literie-verso');
	versoCheckboxes.forEach(checkbox => {
		checkbox.addEventListener('change', function() {
			updateLiteriePriceCalculation();
		});
	});

	// Inițializare
	updateSurface();
	updatePriceCalculation();
	updateTextilePriceCalculation();
	updateLiteriePriceCalculation();

	// Gestionarea trimiterii formularului
	const submitBtn = document.querySelector('.submit-btn');
	if (submitBtn) {
		submitBtn.addEventListener('click', function(e) {
			e.preventDefault();
			submitForm();
		});
	}

	function submitForm() {
		// Colectează datele din formular
		const formData = new FormData();
        
		// Date personale
		formData.append('nom', document.getElementById('nom').value);
		formData.append('courriel', document.getElementById('courriel').value);
		formData.append('telephone', document.getElementById('telephone').value);
		formData.append('date', document.getElementById('date').value);
		formData.append('rue', document.getElementById('rue').value);
		formData.append('ville', document.getElementById('ville').value);
		formData.append('code-postal', document.getElementById('code-postal').value);
		formData.append('message', document.getElementById('message').value);
        
		// Date despre servicii selectate
		formData.append('carpet_selected', selectedCarpet || '');
		formData.append('surface', currentSurface);
		formData.append('steam_cleaner', steamCleanerSelected);
		formData.append('delivery_type', selectedDeliveryType);
		formData.append('delivery_cost', deliveryCost);
        
		// Detectează dacă suntem pe WordPress sau site static
		const isWordPress = typeof ajax_object !== 'undefined';
		// Pentru WordPress AJAX
		if (isWordPress) {
			formData.append('action', 'submit_carpet_form');
			formData.append('nonce', ajax_object.nonce); // Adăugat în WordPress
		}
		const submitUrl = isWordPress ? ajax_object.ajax_url : 'submit-form.php';
        
		// Trimite la server
		fetch(submitUrl, {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				alert('Demanda a fost trimisă cu succes!');
				// Reset formular dacă există un formular cu acest ID
				const formEl = document.getElementById('carpet-form');
				if (formEl && typeof formEl.reset === 'function') {
					formEl.reset();
				}
				// Sau redirect către pagina de mulțumire
				// window.location.href = '/merci.html';
			} else {
				alert('Eroare la trimiterea formularului: ' + (data.error || data.data));
			}
		})
		.catch(error => {
			console.error('Error:', error);
			alert('Eroare la trimiterea formularului');
		});
	}
});