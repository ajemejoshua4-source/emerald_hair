import './bootstrap';

// Alpine could be initialized here if needed
import './bootstrap';

const money = n => `UGX ${new Intl.NumberFormat('en-US').format(n)}`;
const $ = s => document.querySelector(s);
const $$ = s => [...document.querySelectorAll(s)];

let cart = JSON.parse(localStorage.getItem('emeraldCart') || '[]');
let activeProduct = null;

function saveCart(){ localStorage.setItem('emeraldCart', JSON.stringify(cart)); renderCart(); }
function toast(message){ const el=$('#toast'); el.textContent=message; el.classList.add('show'); setTimeout(()=>el.classList.remove('show'),2200); }
function productData(id){ const card=document.querySelector(`.product-card[data-id="${id}"]`); return card ? {id, name:card.dataset.name, price:+card.dataset.price, image:card.dataset.image} : null; }
function addToCart(id){ const p=productData(id); if(!p)return; const found=cart.find(x=>x.id===id); found ? found.qty++ : cart.push({...p,qty:1}); saveCart(); toast(`${p.name} added to cart`); openCart(); }
function renderCart(){
  const count=cart.reduce((a,x)=>a+x.qty,0); $('#cartCount').textContent=count;
  const box=$('#cartItems');
  if(!cart.length){box.innerHTML='<div class="empty-cart"><div>♛</div><h3>Your crown is waiting.</h3><p>You haven\'t added anything yet.</p><a href="#shop" id="startShopping">Start Shopping</a></div>'; $('#cartTotal').textContent='UGX 0'; return;}
  box.innerHTML=cart.map(x=>`<div class="cart-line"><img src="${x.image}" alt="${x.name}"><div><h4>${x.name}</h4><small>Premium selection</small><strong>${money(x.price*x.qty)}</strong><div class="qty"><button data-dec="${x.id}">−</button><span>${x.qty}</span><button data-inc="${x.id}">+</button><button class="remove" data-remove="${x.id}">Remove</button></div></div><span></span></div>`).join('');
  $('#cartTotal').textContent=money(cart.reduce((a,x)=>a+x.price*x.qty,0));
  $$('[data-inc]').forEach(b=>b.onclick=()=>{const x=cart.find(i=>i.id===b.dataset.inc);x.qty++;saveCart()});
  $$('[data-dec]').forEach(b=>b.onclick=()=>{const x=cart.find(i=>i.id===b.dataset.dec);x.qty--;if(x.qty<=0)cart=cart.filter(i=>i.id!==b.dataset.dec);saveCart()});
  $$('[data-remove]').forEach(b=>b.onclick=()=>{cart=cart.filter(i=>i.id!==b.dataset.remove);saveCart();toast('Item removed')});
}
function openCart(){ $('#cartDrawer').classList.add('open'); $('#cartOverlay').classList.add('open'); document.body.style.overflow='hidden'; }
function closeCart(){ $('#cartDrawer').classList.remove('open'); $('#cartOverlay').classList.remove('open'); document.body.style.overflow=''; }

$('#cartBtn').onclick=openCart; $('#closeCart').onclick=closeCart; $('#cartOverlay').onclick=closeCart;
$('#mobileMenuBtn').onclick=()=>$('#mobileMenu').classList.toggle('open');
$$('.mobile-menu a').forEach(a=>a.onclick=()=>$('#mobileMenu').classList.remove('open'));
$('#closeAnnouncement').onclick=()=>document.querySelector('.announcement').remove();
window.addEventListener('scroll',()=>$('#header').classList.toggle('scrolled',scrollY>30));

$$('[data-add]').forEach(b=>b.onclick=()=>addToCart(b.dataset.add));
$$('.filter').forEach(btn=>btn.onclick=()=>{ $$('.filter').forEach(x=>x.classList.remove('active'));btn.classList.add('active');const f=btn.dataset.filter;$$('.product-card').forEach(c=>c.style.display=(f==='all'||c.dataset.category===f)?'':'none'); });
$$('.collection-card').forEach(card=>card.onclick=()=>{const f=card.dataset.filter;const btn=document.querySelector(`.filter[data-filter="${f}"]`);if(btn)btn.click()});

function openModal(id){ const p=productData(id); if(!p)return; activeProduct=p; $('#modalImage').src=p.image;$('#modalImage').alt=p.name;$('#modalName').textContent=p.name;$('#modalPrice').textContent=money(p.price);$('#productModal').classList.add('open');document.body.style.overflow='hidden'; }
function closeModal(){ $('#productModal').classList.remove('open');document.body.style.overflow=''; }
$$('[data-quick]').forEach(b=>b.onclick=e=>{e.stopPropagation();openModal(b.dataset.quick)});
$('#modalClose').onclick=closeModal;$('.modal-backdrop').onclick=closeModal;$('#modalAdd').onclick=()=>{if(activeProduct){addToCart(activeProduct.id);closeModal()}};

$('#startShopping')?.addEventListener('click',closeCart);
$('#checkoutBtn').onclick=()=>{ if(!cart.length){toast('Add a product before checkout');return;} const total=cart.reduce((a,x)=>a+x.price*x.qty,0); window.open(`https://wa.me/2567069133416?text=${encodeURIComponent(`Hello Emerald Hair 👑 I would like to place an order. My cart total is ${money(total)}.`)}`,'_blank'); };
$('#searchBtn').onclick=()=>{document.querySelector('#shop').scrollIntoView(); const input=document.createElement('input'); input.className='site-search'; input.placeholder='Search products...'; input.style.cssText='position:fixed;top:88px;left:50%;transform:translateX(-50%);z-index:70;width:min(90%,520px);padding:16px;background:#101713;color:#fff;border:1px solid #c9a227;outline:0';document.body.appendChild(input);input.focus();input.oninput=()=>{const q=input.value.toLowerCase();$$('.product-card').forEach(c=>c.style.display=c.dataset.name.toLowerCase().includes(q)?'':'none')};input.onkeydown=e=>{if(e.key==='Escape'){input.remove()}}};
$('#newsletterForm').onsubmit=e=>{e.preventDefault();toast('Welcome to the Emerald Inner Circle');e.target.reset()};
$('#contactSubmit').onclick=()=>toast('Thanks! Please use WhatsApp for the fastest response.');

renderCart();
