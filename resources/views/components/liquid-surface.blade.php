@props([
    'colors'         => [],
    'speed'          => 1.2,
    'intensity'      => 1.8,
    'grainIntensity' => 0.08,
    'gradientSize'   => 0.45,
    'gradientCount'  => 12.0,
    'color1Weight'   => 0.5,
    'color2Weight'   => 1.8,
    'darkNavyColor'  => '#0a0e27',
    'scheme'         => 1,
])

<div
    class="liquid-surface-mount"
    style="position:absolute;inset:0;pointer-events:none;z-index:0;overflow:hidden;border-radius:inherit;"
    data-ls-colors='@json($colors)'
    data-ls-speed="{{ $speed }}"
    data-ls-intensity="{{ $intensity }}"
    data-ls-grain="{{ $grainIntensity }}"
    data-ls-gradient-size="{{ $gradientSize }}"
    data-ls-gradient-count="{{ $gradientCount }}"
    data-ls-color1-weight="{{ $color1Weight }}"
    data-ls-color2-weight="{{ $color2Weight }}"
    data-ls-dark-navy="{{ $darkNavyColor }}"
></div>

@once
<script>
(function () {
    function hexToRgbLs(hex) {
        const r = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return r ? { r: parseInt(r[1], 16) / 255, g: parseInt(r[2], 16) / 255, b: parseInt(r[3], 16) / 255 } : null;
    }

    function __lsInit(mount) {
        if (mount.dataset.lsInit) return;
        mount.dataset.lsInit = '1';
        const THREE = window.THREE;

        const cfg = {
            colors:         JSON.parse(mount.dataset.lsColors || '[]'),
            speed:          parseFloat(mount.dataset.lsSpeed          || '1.2'),
            intensity:      parseFloat(mount.dataset.lsIntensity      || '1.8'),
            grainIntensity: parseFloat(mount.dataset.lsGrain          || '0.08'),
            gradientSize:   parseFloat(mount.dataset.lsGradientSize   || '0.45'),
            gradientCount:  parseFloat(mount.dataset.lsGradientCount  || '12'),
            color1Weight:   parseFloat(mount.dataset.lsColor1Weight   || '0.5'),
            color2Weight:   parseFloat(mount.dataset.lsColor2Weight   || '1.8'),
            darkNavyColor:  mount.dataset.lsDarkNavy || '#0a0e27',
        };

        // ── Renderer ─────────────────────────────────────────────────────────
        const renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' });
        const w0 = mount.clientWidth  || 100;
        const h0 = mount.clientHeight || 100;
        renderer.setSize(w0, h0);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        mount.appendChild(renderer.domElement);

        const camera = new THREE.PerspectiveCamera(45, w0 / h0, 0.1, 10000);
        camera.position.z = 50;

        const scene = new THREE.Scene();
        scene.background = new THREE.Color(parseInt(cfg.darkNavyColor.replace('#', ''), 16));

        const clock = new THREE.Clock();

        // ── Touch Texture ─────────────────────────────────────────────────────
        const TT = { SIZE: 64, MAX_AGE: 64 };
        TT.RADIUS = 0.25 * TT.SIZE;
        TT.SPEED  = 1 / TT.MAX_AGE;
        let ttTrail = [], ttLast = null;
        const ttCanvas = document.createElement('canvas');
        ttCanvas.width = ttCanvas.height = TT.SIZE;
        const ttCtx = ttCanvas.getContext('2d');
        ttCtx.fillStyle = 'black'; ttCtx.fillRect(0, 0, TT.SIZE, TT.SIZE);
        const ttTexture = new THREE.CanvasTexture(ttCanvas);

        function ttDrawPoint(p) {
            let inten = 1;
            if (p.age < TT.MAX_AGE * 0.3) {
                inten = Math.sin((p.age / (TT.MAX_AGE * 0.3)) * (Math.PI / 2));
            } else {
                const t = 1 - (p.age - TT.MAX_AGE * 0.3) / (TT.MAX_AGE * 0.7);
                inten = -t * (t - 2);
            }
            inten *= p.force;
            const col = ((p.vx + 1) / 2 * 255).toFixed(0) + ',' + ((p.vy + 1) / 2 * 255).toFixed(0) + ',' + (inten * 255).toFixed(0);
            const off = TT.SIZE * 5;
            ttCtx.shadowOffsetX = off; ttCtx.shadowOffsetY = off;
            ttCtx.shadowBlur    = TT.RADIUS;
            ttCtx.shadowColor   = 'rgba(' + col + ',' + (0.2 * inten) + ')';
            ttCtx.beginPath();
            ttCtx.fillStyle = 'rgba(255,0,0,1)';
            ttCtx.arc(p.x * TT.SIZE - off, (1 - p.y) * TT.SIZE - off, TT.RADIUS, 0, Math.PI * 2);
            ttCtx.fill();
        }

        function ttUpdate() {
            ttCtx.fillStyle = 'black'; ttCtx.fillRect(0, 0, TT.SIZE, TT.SIZE);
            for (let i = ttTrail.length - 1; i >= 0; i--) {
                const p = ttTrail[i];
                const f = p.force * TT.SPEED * (1 - p.age / TT.MAX_AGE);
                p.x += p.vx * f; p.y += p.vy * f; p.age++;
                if (p.age > TT.MAX_AGE) ttTrail.splice(i, 1);
                else ttDrawPoint(p);
            }
            ttTexture.needsUpdate = true;
        }

        function ttAddTouch(pt) {
            let force = 0, vx = 0, vy = 0;
            if (ttLast) {
                const dx = pt.x - ttLast.x, dy = pt.y - ttLast.y;
                if (dx === 0 && dy === 0) return;
                const d = Math.sqrt(dx * dx + dy * dy);
                vx = dx / d; vy = dy / d;
                force = Math.min((dx * dx + dy * dy) * 20000, 2.0);
            }
            ttLast = { x: pt.x, y: pt.y };
            ttTrail.push({ x: pt.x, y: pt.y, age: 0, force, vx, vy });
        }

        // ── Uniforms ──────────────────────────────────────────────────────────
        const uniforms = {
            uTime:          { value: 0 },
            uResolution:    { value: new THREE.Vector2(w0, h0) },
            uColor1:        { value: new THREE.Vector3(0.145, 0.388, 0.922) },
            uColor2:        { value: new THREE.Vector3(0.118, 0.220, 0.541) },
            uColor3:        { value: new THREE.Vector3(0.231, 0.306, 0.792) },
            uColor4:        { value: new THREE.Vector3(0.118, 0.220, 0.541) },
            uColor5:        { value: new THREE.Vector3(0.113, 0.306, 0.847) },
            uColor6:        { value: new THREE.Vector3(0.310, 0.275, 0.898) },
            uSpeed:         { value: cfg.speed },
            uIntensity:     { value: cfg.intensity },
            uTouchTexture:  { value: ttTexture },
            uGrainIntensity:{ value: cfg.grainIntensity },
            uZoom:          { value: 1.0 },
            uDarkNavy:      { value: new THREE.Vector3(0.118, 0.220, 0.541) },
            uGradientSize:  { value: cfg.gradientSize },
            uGradientCount: { value: cfg.gradientCount },
            uColor1Weight:  { value: cfg.color1Weight },
            uColor2Weight:  { value: cfg.color2Weight },
        };

        const dn = hexToRgbLs(cfg.darkNavyColor);
        if (dn) uniforms.uDarkNavy.value.set(dn.r, dn.g, dn.b);

        if (cfg.colors.length > 0) {
            const parsed = cfg.colors.map(hexToRgbLs).filter(Boolean);
            ['uColor1','uColor2','uColor3','uColor4','uColor5','uColor6'].forEach(function (name, i) {
                const rgb = parsed[i % parsed.length];
                if (rgb) uniforms[name].value.set(rgb.r, rgb.g, rgb.b);
            });
        }

        // ── Shaders ───────────────────────────────────────────────────────────
        const vertexShader = [
            'varying vec2 vUv;',
            'void main() {',
            '    gl_Position = projectionMatrix * modelViewMatrix * vec4(position.xyz, 1.);',
            '    vUv = uv;',
            '}'
        ].join('\n');

        const fragmentShader = [
            'uniform float uTime;',
            'uniform vec2 uResolution;',
            'uniform vec3 uColor1; uniform vec3 uColor2; uniform vec3 uColor3;',
            'uniform vec3 uColor4; uniform vec3 uColor5; uniform vec3 uColor6;',
            'uniform float uSpeed; uniform float uIntensity; uniform sampler2D uTouchTexture;',
            'uniform float uGrainIntensity; uniform float uZoom; uniform vec3 uDarkNavy;',
            'uniform float uGradientSize; uniform float uGradientCount;',
            'uniform float uColor1Weight; uniform float uColor2Weight;',
            'varying vec2 vUv;',
            '#define PI 3.14159265359',

            'float grain(vec2 uv, float time) {',
            '    vec2 g = uv * uResolution * 0.5;',
            '    return fract(sin(dot(g + time, vec2(12.9898, 78.233))) * 43758.5453) * 2.0 - 1.0;',
            '}',

            'vec3 getGradientColor(vec2 uv, float time) {',
            '    float gr = uGradientSize;',
            '    vec2 c1  = vec2(0.5 + sin(time*uSpeed*0.4)*0.4,  0.5 + cos(time*uSpeed*0.5)*0.4);',
            '    vec2 c2  = vec2(0.5 + cos(time*uSpeed*0.6)*0.5,  0.5 + sin(time*uSpeed*0.45)*0.5);',
            '    vec2 c3  = vec2(0.5 + sin(time*uSpeed*0.35)*0.45,0.5 + cos(time*uSpeed*0.55)*0.45);',
            '    vec2 c4  = vec2(0.5 + cos(time*uSpeed*0.5)*0.4,  0.5 + sin(time*uSpeed*0.4)*0.4);',
            '    vec2 c5  = vec2(0.5 + sin(time*uSpeed*0.7)*0.35, 0.5 + cos(time*uSpeed*0.6)*0.35);',
            '    vec2 c6  = vec2(0.5 + cos(time*uSpeed*0.45)*0.5, 0.5 + sin(time*uSpeed*0.65)*0.5);',
            '    vec2 c7  = vec2(0.5 + sin(time*uSpeed*0.55)*0.38,0.5 + cos(time*uSpeed*0.48)*0.42);',
            '    vec2 c8  = vec2(0.5 + cos(time*uSpeed*0.65)*0.36,0.5 + sin(time*uSpeed*0.52)*0.44);',
            '    vec2 c9  = vec2(0.5 + sin(time*uSpeed*0.42)*0.41,0.5 + cos(time*uSpeed*0.58)*0.39);',
            '    vec2 c10 = vec2(0.5 + cos(time*uSpeed*0.48)*0.37,0.5 + sin(time*uSpeed*0.62)*0.43);',
            '    vec2 c11 = vec2(0.5 + sin(time*uSpeed*0.68)*0.33,0.5 + cos(time*uSpeed*0.44)*0.46);',
            '    vec2 c12 = vec2(0.5 + cos(time*uSpeed*0.38)*0.39,0.5 + sin(time*uSpeed*0.56)*0.41);',

            '    float i1  = 1.0-smoothstep(0.0,gr,length(uv-c1));',
            '    float i2  = 1.0-smoothstep(0.0,gr,length(uv-c2));',
            '    float i3  = 1.0-smoothstep(0.0,gr,length(uv-c3));',
            '    float i4  = 1.0-smoothstep(0.0,gr,length(uv-c4));',
            '    float i5  = 1.0-smoothstep(0.0,gr,length(uv-c5));',
            '    float i6  = 1.0-smoothstep(0.0,gr,length(uv-c6));',
            '    float i7  = 1.0-smoothstep(0.0,gr,length(uv-c7));',
            '    float i8  = 1.0-smoothstep(0.0,gr,length(uv-c8));',
            '    float i9  = 1.0-smoothstep(0.0,gr,length(uv-c9));',
            '    float i10 = 1.0-smoothstep(0.0,gr,length(uv-c10));',
            '    float i11 = 1.0-smoothstep(0.0,gr,length(uv-c11));',
            '    float i12 = 1.0-smoothstep(0.0,gr,length(uv-c12));',

            '    vec2 ru1 = uv - 0.5;',
            '    float a1 = time*uSpeed*0.15;',
            '    ru1 = vec2(ru1.x*cos(a1)-ru1.y*sin(a1), ru1.x*sin(a1)+ru1.y*cos(a1)) + 0.5;',
            '    vec2 ru2 = uv - 0.5;',
            '    float a2 = -time*uSpeed*0.12;',
            '    ru2 = vec2(ru2.x*cos(a2)-ru2.y*sin(a2), ru2.x*sin(a2)+ru2.y*cos(a2)) + 0.5;',
            '    float ri1 = 1.0-smoothstep(0.0,0.8,length(ru1-0.5));',
            '    float ri2 = 1.0-smoothstep(0.0,0.8,length(ru2-0.5));',

            '    vec3 color = vec3(0.0);',
            '    color += uColor1*i1*(0.55+0.45*sin(time*uSpeed))*uColor1Weight;',
            '    color += uColor2*i2*(0.55+0.45*cos(time*uSpeed*1.2))*uColor2Weight;',
            '    color += uColor3*i3*(0.55+0.45*sin(time*uSpeed*0.8))*uColor1Weight;',
            '    color += uColor4*i4*(0.55+0.45*cos(time*uSpeed*1.3))*uColor2Weight;',
            '    color += uColor5*i5*(0.55+0.45*sin(time*uSpeed*1.1))*uColor1Weight;',
            '    color += uColor6*i6*(0.55+0.45*cos(time*uSpeed*0.9))*uColor2Weight;',
            '    if (uGradientCount > 6.0) {',
            '        color += uColor1*i7*(0.55+0.45*sin(time*uSpeed*1.4))*uColor1Weight;',
            '        color += uColor2*i8*(0.55+0.45*cos(time*uSpeed*1.5))*uColor2Weight;',
            '        color += uColor3*i9*(0.55+0.45*sin(time*uSpeed*1.6))*uColor1Weight;',
            '        color += uColor4*i10*(0.55+0.45*cos(time*uSpeed*1.7))*uColor2Weight;',
            '    }',
            '    if (uGradientCount > 10.0) {',
            '        color += uColor5*i11*(0.55+0.45*sin(time*uSpeed*1.8))*uColor1Weight;',
            '        color += uColor6*i12*(0.55+0.45*cos(time*uSpeed*1.9))*uColor2Weight;',
            '    }',
            '    color += mix(uColor1,uColor3,ri1)*0.45*uColor1Weight;',
            '    color += mix(uColor2,uColor4,ri2)*0.4*uColor2Weight;',

            '    color = clamp(color,vec3(0.0),vec3(1.0))*uIntensity;',
            '    float lum = dot(color,vec3(0.299,0.587,0.114));',
            '    color = mix(vec3(lum),color,1.35);',
            '    color = pow(color,vec3(0.92));',
            '    float b1 = length(color);',
            '    color = mix(uDarkNavy,color,max(b1*1.2,0.15));',
            '    float b2 = length(color);',
            '    if (b2 > 1.0) color = color*(1.0/b2);',
            '    return color;',
            '}',

            'void main() {',
            '    vec2 uv = vUv;',
            '    vec4 tt = texture2D(uTouchTexture, uv);',
            '    float vx = -(tt.r*2.0-1.0);',
            '    float vy = -(tt.g*2.0-1.0);',
            '    float fi = tt.b;',
            '    uv.x += vx*0.8*fi;',
            '    uv.y += vy*0.8*fi;',
            '    float dist = length(uv-vec2(0.5));',
            '    uv += vec2(sin(dist*20.0-uTime*3.0)*0.04*fi + sin(dist*15.0-uTime*2.0)*0.03*fi);',
            '    vec3 color = getGradientColor(uv, uTime);',
            '    color += grain(uv,uTime)*uGrainIntensity;',
            '    float ts = uTime*0.5;',
            '    color.r += sin(ts)*0.02;',
            '    color.g += cos(ts*1.4)*0.02;',
            '    color.b += sin(ts*1.2)*0.02;',
            '    float b3 = length(color);',
            '    color = mix(uDarkNavy,color,max(b3*1.2,0.15));',
            '    color = clamp(color,vec3(0.0),vec3(1.0));',
            '    float b4 = length(color);',
            '    if (b4 > 1.0) color = color*(1.0/b4);',
            '    gl_FragColor = vec4(color, 1.0);',
            '}'
        ].join('\n');

        // ── Mesh ─────────────────────────────────────────────────────────────
        function getViewSize() {
            const fovRad = (camera.fov * Math.PI) / 180;
            const h = Math.abs(camera.position.z * Math.tan(fovRad / 2) * 2);
            return { width: h * camera.aspect, height: h };
        }

        const vs = getViewSize();
        const material = new THREE.ShaderMaterial({ uniforms, vertexShader, fragmentShader });
        const mesh = new THREE.Mesh(new THREE.PlaneGeometry(vs.width, vs.height, 1, 1), material);
        scene.add(mesh);

        // ── Render Loop ───────────────────────────────────────────────────────
        let rafId = null;
        function tick() {
            const delta = Math.min(clock.getDelta(), 0.1);
            ttUpdate();
            uniforms.uTime.value += delta;
            renderer.render(scene, camera);
            rafId = requestAnimationFrame(tick);
        }
        tick();

        // ── Resize ────────────────────────────────────────────────────────────
        function onResize() {
            const w = mount.clientWidth, h = mount.clientHeight;
            if (!w || !h) return;
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            renderer.setSize(w, h);
            uniforms.uResolution.value.set(w, h);
            const vs2 = getViewSize();
            mesh.geometry.dispose();
            mesh.geometry = new THREE.PlaneGeometry(vs2.width, vs2.height, 1, 1);
        }
        const ro = new ResizeObserver(onResize);
        ro.observe(mount);
        setTimeout(onResize, 50);

        // ── Mouse / Touch ─────────────────────────────────────────────────────
        const parent = mount.parentElement;
        if (parent) {
            parent.addEventListener('mousemove', function (e) {
                const rect = mount.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                ttAddTouch({ x: (e.clientX - rect.left) / rect.width, y: 1 - (e.clientY - rect.top) / rect.height });
            });
            parent.addEventListener('touchmove', function (e) {
                const rect = mount.getBoundingClientRect();
                if (!rect.width || !rect.height) return;
                ttAddTouch({ x: (e.touches[0].clientX - rect.left) / rect.width, y: 1 - (e.touches[0].clientY - rect.top) / rect.height });
            }, { passive: false });
        }

        // ── Visibility pause/resume ───────────────────────────────────────────
        new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) { if (!rafId) tick(); }
                else { if (rafId) { cancelAnimationFrame(rafId); rafId = null; } }
            });
        }).observe(mount);
    }

    function __lsInitAll() {
        if (!window.THREE) return;
        document.querySelectorAll('.liquid-surface-mount:not([data-ls-init])').forEach(__lsInit);
    }

    // Load Three.js (reuse if already present from liquid-ether)
    if (window.THREE) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', __lsInitAll);
        } else {
            __lsInitAll();
        }
    } else {
        var existing = document.getElementById('__ls-three') || document.getElementById('__le-three');
        if (existing) {
            existing.addEventListener('load', __lsInitAll);
        } else {
            var s = document.createElement('script');
            s.id  = '__ls-three';
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js';
            s.onload = __lsInitAll;
            document.head.appendChild(s);
        }
    }

    window.__lsInitAll = __lsInitAll;
})();
</script>
@endonce
