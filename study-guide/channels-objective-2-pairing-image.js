(() => {
  'use strict';
  const root=document.getElementById('channels-objective-2');
  const flow=root?.querySelector('.paired-circuit-flow');
  if(!flow||document.getElementById('primary-channel-pairing-diagram'))return;
  const figure=document.createElement('figure');
  figure.id='primary-channel-pairing-diagram';
  figure.className='visual-student primary-channel-pairing-diagram';
  figure.innerHTML=`<h5>Three Circuits — Interior–Exterior and Six-Channel Pairings</h5><img src="assets/primary-channel-three-circuits-pairings.webp?v=1" loading="lazy" alt="Diagram of the three circuits of the twelve Primary Channels. Orange brackets on the right identify interior–exterior pairs, while teal brackets on the left identify Hand and Foot channels sharing the same six-channel identity. The final arrow returns from the Liver Channel to the Lung Channel."><figcaption><strong>Read both sides of each circuit:</strong> orange brackets show the interior–exterior Yin–Yang relationship on the same limb; teal brackets show the Hand–Foot pairing that shares the same Taiyin, Yangming, Shaoyin, Taiyang, Jueyin or Shaoyang identity. The return arrow completes the continuous sequence from Liver back to Lung.</figcaption><p class="pairing-diagram-note">Conceptual relationship map—not a point-location or anatomical pathway diagram.</p><button type="button">Enlarge image</button>`;
  figure.querySelector('button').addEventListener('click',()=>figure.querySelector('img').click());
  flow.after(figure);
  const style=document.createElement('style');
  style.textContent='.primary-channel-pairing-diagram{margin:18px 0;padding:16px;border:1px solid #bfd5d3;border-radius:14px;background:#f7fbfb;box-shadow:0 7px 18px rgba(11,64,74,.07)}.primary-channel-pairing-diagram h5{margin:0 0 12px;color:#075f60;font-size:1.08rem}.primary-channel-pairing-diagram img{display:block;width:100%;height:auto;border-radius:9px;background:#fff;cursor:zoom-in}.primary-channel-pairing-diagram figcaption{margin:13px 0 8px;line-height:1.55;color:#263f4b}.primary-channel-pairing-diagram .pairing-diagram-note{margin:8px 0 13px;padding:9px 12px;border-left:4px solid #ef7e2d;border-radius:6px;background:#fff8ef}.primary-channel-pairing-diagram button{padding:9px 14px;border:0;border-radius:8px;background:#087774;color:#fff;font-weight:800;cursor:pointer}.primary-channel-pairing-diagram button:hover,.primary-channel-pairing-diagram button:focus-visible{background:#056461}';
  document.head.append(style);
})();
