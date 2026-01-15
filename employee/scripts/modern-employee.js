/**
 * Modern Employee Area JavaScript
 * Handles interactive features and modal functionality
 */

// Modal Management
const ModalManager = {
	overlay: null,
	container: null,
	
	init: function() {
		// Create modal overlay if it doesn't exist
		if (!document.getElementById('employee-modal-overlay')) {
			const overlay = document.createElement('div');
			overlay.id = 'employee-modal-overlay';
			overlay.className = 'modal-overlay';
			overlay.innerHTML = `
				<div class="modal-container">
					<div class="modal-header">
						<h3 id="modal-title">Confirm Action</h3>
					</div>
					<div class="modal-body">
						<p id="modal-message">Are you sure you want to proceed?</p>
					</div>
					<div class="modal-footer">
						<button class="modal-btn secondary" id="modal-cancel-btn">Cancel</button>
						<button class="modal-btn primary" id="modal-confirm-btn">Confirm</button>
					</div>
				</div>
			`;
			document.body.appendChild(overlay);
			this.overlay = overlay;
			this.container = overlay.querySelector('.modal-container');
			
			// Close modal on overlay click
			overlay.addEventListener('click', function(e) {
				if (e.target === overlay) {
					ModalManager.close();
				}
			});
			
			// Close modal on cancel button
			document.getElementById('modal-cancel-btn').addEventListener('click', function() {
				ModalManager.close();
			});
		} else {
			this.overlay = document.getElementById('employee-modal-overlay');
			this.container = this.overlay.querySelector('.modal-container');
		}
	},
	
	show: function(title, message, onConfirm, onCancel) {
		this.init();
		
		document.getElementById('modal-title').textContent = title;
		document.getElementById('modal-message').textContent = message;
		
		// Remove previous event listeners
		const confirmBtn = document.getElementById('modal-confirm-btn');
		const newConfirmBtn = confirmBtn.cloneNode(true);
		confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
		
		// Add new event listener
		newConfirmBtn.addEventListener('click', function() {
			if (onConfirm && typeof onConfirm === 'function') {
				onConfirm();
			}
			ModalManager.close();
		});
		
		// Show modal
		this.overlay.classList.add('active');
		document.body.style.overflow = 'hidden';
	},
	
	close: function() {
		if (this.overlay) {
			this.overlay.classList.remove('active');
			document.body.style.overflow = '';
		}
	}
};

// Mark as Read Confirmation
function confirmMarkAsRead(messageId, baseUrl) {
	// Use provided baseUrl or construct from current location
	if (!baseUrl) {
		baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	}
	
	ModalManager.show(
		'Mark as Read',
		'Are you sure you want to mark this message as read?',
		function() {
			window.location.href = baseUrl + '?messageId=' + messageId + '&operation=1';
		}
	);
}

// Delete Message Confirmation
function confirmDeleteMessage(messageId, baseUrl) {
	// Use provided baseUrl or construct from current location
	if (!baseUrl) {
		baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
	}
	
	ModalManager.show(
		'Delete Message',
		'Are you sure you want to delete this message?',
		function() {
			window.location.href = baseUrl + '?messageId=' + messageId + '&operation=2';
		}
	);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
	// Initialize modal
	ModalManager.init();
	
	// Add smooth scrolling
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function(e) {
			const href = this.getAttribute('href');
			if (href !== '#' && href.length > 1) {
				const target = document.querySelector(href);
				if (target) {
					e.preventDefault();
					target.scrollIntoView({
						behavior: 'smooth',
						block: 'start'
					});
				}
			}
		});
	});
	
	// Add loading states to buttons
	document.querySelectorAll('.message-action-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			if (!this.classList.contains('loading')) {
				this.classList.add('loading');
				const originalText = this.textContent;
				this.textContent = 'Processing...';
				
				// Reset after 3 seconds if still on page
				setTimeout(() => {
					if (this.classList.contains('loading')) {
						this.classList.remove('loading');
						this.textContent = originalText;
					}
				}, 3000);
			}
		});
	});
	
	// Add fade-in animation to cards
	const cards = document.querySelectorAll('.quick-links-card, .messages-card, .ratings-card');
	cards.forEach((card, index) => {
		card.style.opacity = '0';
		card.style.transform = 'translateY(20px)';
		card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
		
		setTimeout(() => {
			card.style.opacity = '1';
			card.style.transform = 'translateY(0)';
		}, index * 100);
	});
	
	// Mobile menu toggle (if needed)
	const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
	if (mobileMenuToggle) {
		mobileMenuToggle.addEventListener('click', function() {
			const menu = document.getElementById('mobile-menu');
			if (menu) {
				menu.classList.toggle('active');
			}
		});
	}
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape' && ModalManager.overlay && ModalManager.overlay.classList.contains('active')) {
		ModalManager.close();
	}
});

// Modern Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
	const navToggle = document.getElementById('modernNavToggle');
	const navMenu = document.getElementById('modernNavMenu');
	const navDropdowns = document.querySelectorAll('.modern-nav-dropdown');
	
	if(navToggle && navMenu) {
		navToggle.addEventListener('click', function() {
			navToggle.classList.toggle('active');
			navMenu.classList.toggle('active');
		});
		
		// Close menu when clicking outside
		document.addEventListener('click', function(e) {
			if(!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
				navToggle.classList.remove('active');
				navMenu.classList.remove('active');
			}
		});
	}
	
	// Mobile dropdown toggle
	if(window.innerWidth <= 768) {
		navDropdowns.forEach(dropdown => {
			const link = dropdown.querySelector('.modern-nav-link');
			if(link) {
				link.addEventListener('click', function(e) {
					if(window.innerWidth <= 768) {
						e.preventDefault();
						dropdown.classList.toggle('active');
					}
				});
			}
		});
	}
	
	// Handle window resize
	let resizeTimer;
	window.addEventListener('resize', function() {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(function() {
			if(window.innerWidth > 768) {
				navDropdowns.forEach(dropdown => {
					dropdown.classList.remove('active');
				});
				if(navToggle) navToggle.classList.remove('active');
				if(navMenu) navMenu.classList.remove('active');
			}
		}, 250);
	});
});

// Export functions for global use
window.confirmMarkAsRead = confirmMarkAsRead;
window.confirmDeleteMessage = confirmDeleteMessage;
window.ModalManager = ModalManager;

