@props([
    'colors'     => ['#5227FF', '#FF9FFC', '#B19EEF'],
    'opacity'    => 0.20,
    'mouseForce' => 20,
    'cursorSize' => 50,
    'autoDemo'   => true,
    'autoSpeed'  => 0.2,
    'resolution' => 0.5,
])

<div
    class="liquid-ether-mount"
    data-le-colors='@json($colors)'
    data-le-opacity="{{ $opacity }}"
    data-le-mouse-force="{{ $mouseForce }}"
    data-le-cursor-size="{{ $cursorSize }}"
    data-le-auto-demo="{{ $autoDemo ? 'true' : 'false' }}"
    data-le-auto-speed="{{ $autoSpeed }}"
    data-le-resolution="{{ $resolution }}"
    style="position:absolute;inset:0;pointer-events:none;z-index:1;overflow:hidden;border-radius:inherit;"
></div>

@once
<script>
(function(){
function __leInit(mount){
    if(!window.THREE)return;
    var THREE=window.THREE;
    var colors=JSON.parse(mount.dataset.leColors||'["#5227FF","#FF9FFC","#B19EEF"]');
    var opacity=parseFloat(mount.dataset.leOpacity||'0.20');
    var mouseForce=parseFloat(mount.dataset.leMouseForce||'20');
    var cursorSize=parseFloat(mount.dataset.leCursorSize||'50');
    var autoDemo=mount.dataset.leAutoDemo!=='false';
    var autoSpeed=parseFloat(mount.dataset.leAutoSpeed||'0.2');
    var resolution=parseFloat(mount.dataset.leResolution||'0.5');
    var dt=0.014,BFECC=true,isViscous=false,isBounce=false;
    var iterPoisson=32,iterViscous=32,viscousVal=30;
    var autoIntensity=2.2,takeoverDur=0.25,autoResumeDelay=1000,autoRampDur=0.6;

    mount.style.opacity=opacity;

    /* ── Palette ── */
    function makePalette(stops){
        var arr=stops&&stops.length>1?stops:stops&&stops.length===1?[stops[0],stops[0]]:['#fff','#fff'];
        var w=arr.length,data=new Uint8Array(w*4);
        for(var i=0;i<w;i++){var c=new THREE.Color(arr[i]);data[i*4]=Math.round(c.r*255);data[i*4+1]=Math.round(c.g*255);data[i*4+2]=Math.round(c.b*255);data[i*4+3]=255;}
        var t=new THREE.DataTexture(data,w,1,THREE.RGBAFormat);
        t.magFilter=t.minFilter=THREE.LinearFilter;t.wrapS=t.wrapT=THREE.ClampToEdgeWrapping;t.generateMipmaps=false;t.needsUpdate=true;return t;
    }
    var paletteTex=makePalette(colors);
    var bgVec4=new THREE.Vector4(0,0,0,0);

    /* ── Renderer ── */
    var W=1,H=1,pixelRatio=Math.min(window.devicePixelRatio||1,2);
    var renderer=new THREE.WebGLRenderer({antialias:true,alpha:true});
    renderer.autoClear=false;renderer.setClearColor(new THREE.Color(0),0);
    renderer.setPixelRatio(pixelRatio);
    renderer.domElement.style.cssText='width:100%;height:100%;display:block;';
    mount.appendChild(renderer.domElement);
    var clock=new THREE.Clock();clock.start();
    var time=0;

    function doResize(){
        var r=mount.getBoundingClientRect();
        W=Math.max(1,Math.floor(r.width));H=Math.max(1,Math.floor(r.height));
        renderer.setSize(W,H,false);
    }
    doResize();

    /* ── Mouse ── */
    var mCoords=new THREE.Vector2(),mOld=new THREE.Vector2(),mDiff=new THREE.Vector2();
    var mInside=false,mHasUser=false,mIsAuto=false,mTakeover=false;
    var mTakeoverStart=0,mTakeoverFrom=new THREE.Vector2(),mTakeoverTo=new THREE.Vector2();
    var mTimer=null,lastInteract=performance.now();

    function mSetCoords(x,y){
        if(mTimer)clearTimeout(mTimer);
        var r=mount.getBoundingClientRect();if(!r.width||!r.height)return;
        mCoords.set((x-r.left)/r.width*2-1,-((y-r.top)/r.height*2-1));
        mTimer=setTimeout(function(){},100);
    }
    function mSetNorm(nx,ny){mCoords.set(nx,ny);}
    function mIsInside(cx,cy){var r=mount.getBoundingClientRect();return cx>=r.left&&cx<=r.right&&cy>=r.top&&cy<=r.bottom;}

    function onMM(e){
        mInside=mIsInside(e.clientX,e.clientY);if(!mInside)return;
        lastInteract=performance.now();autoActive=false;
        if(mIsAuto&&!mHasUser&&!mTakeover){
            var r=mount.getBoundingClientRect();if(!r.width||!r.height)return;
            mTakeoverFrom.copy(mCoords);
            mTakeoverTo.set((e.clientX-r.left)/r.width*2-1,-((e.clientY-r.top)/r.height*2-1));
            mTakeoverStart=performance.now();mTakeover=true;mHasUser=true;mIsAuto=false;return;
        }
        mSetCoords(e.clientX,e.clientY);mHasUser=true;
    }
    function onML(){mInside=false;}
    window.addEventListener('mousemove',onMM);
    window.addEventListener('mouseleave',onML);

    function mouseUpdate(){
        if(mTakeover){
            var t=(performance.now()-mTakeoverStart)/(takeoverDur*1000);
            if(t>=1){mTakeover=false;mCoords.copy(mTakeoverTo);mOld.copy(mCoords);mDiff.set(0,0);}
            else{var k=t*t*(3-2*t);mCoords.copy(mTakeoverFrom).lerp(mTakeoverTo,k);}
        }
        mDiff.subVectors(mCoords,mOld);mOld.copy(mCoords);
        if(mOld.x===0&&mOld.y===0)mDiff.set(0,0);
        if(mIsAuto&&!mTakeover)mDiff.multiplyScalar(autoIntensity);
    }

    /* ── AutoDriver ── */
    var autoActive=false,autoPos=new THREE.Vector2(),autoTgt=new THREE.Vector2();
    var autoLast=performance.now(),autoActTime=0,autoRampMs=autoRampDur*1000;
    var tmpDir=new THREE.Vector2();
    function autoPick(){autoTgt.set((Math.random()*2-1)*0.8,(Math.random()*2-1)*0.8);}
    autoPick();

    function autoUpdate(){
        if(!autoDemo)return;
        var now=performance.now();
        if(now-lastInteract<autoResumeDelay){if(autoActive){autoActive=false;mIsAuto=false;}return;}
        if(mInside){if(autoActive){autoActive=false;mIsAuto=false;}return;}
        if(!autoActive){autoActive=true;autoPos.copy(mCoords);autoLast=now;autoActTime=now;}
        mIsAuto=true;
        var ds=(now-autoLast)/1000;autoLast=now;if(ds>0.2)ds=0.016;
        var dir=tmpDir.subVectors(autoTgt,autoPos),dist=dir.length();
        if(dist<0.01){autoPick();return;}
        dir.normalize();
        var ramp=1;if(autoRampMs>0){var t=Math.min(1,(now-autoActTime)/autoRampMs);ramp=t*t*(3-2*t);}
        autoPos.addScaledVector(dir,Math.min(autoSpeed*ds*ramp,dist));
        mSetNorm(autoPos.x,autoPos.y);
    }

    /* ── Shaders ── */
    var face_vert='attribute vec3 position;uniform vec2 px;uniform vec2 boundarySpace;varying vec2 uv;precision highp float;void main(){vec3 pos=position;vec2 scale=1.0-boundarySpace*2.0;pos.xy=pos.xy*scale;uv=vec2(0.5)+(pos.xy)*0.5;gl_Position=vec4(pos,1.0);}';
    var line_vert='attribute vec3 position;uniform vec2 px;precision highp float;varying vec2 uv;void main(){vec3 pos=position;uv=0.5+pos.xy*0.5;vec2 n=sign(pos.xy);pos.xy=abs(pos.xy)-px*1.0;pos.xy*=n;gl_Position=vec4(pos,1.0);}';
    var mouse_vert='precision highp float;attribute vec3 position;attribute vec2 uv;uniform vec2 center;uniform vec2 scale;uniform vec2 px;varying vec2 vUv;void main(){vec2 pos=position.xy*scale*2.0*px+center;vUv=uv;gl_Position=vec4(pos,0.0,1.0);}';
    var adv_frag='precision highp float;uniform sampler2D velocity;uniform float dt;uniform bool isBFECC;uniform vec2 fboSize;uniform vec2 px;varying vec2 uv;void main(){vec2 ratio=max(fboSize.x,fboSize.y)/fboSize;if(isBFECC==false){vec2 vel=texture2D(velocity,uv).xy;vec2 uv2=uv-vel*dt*ratio;gl_FragColor=vec4(texture2D(velocity,uv2).xy,0.0,0.0);}else{vec2 sn=uv;vec2 vo=texture2D(velocity,uv).xy;vec2 so=sn-vo*dt*ratio;vec2 vn1=texture2D(velocity,so).xy;vec2 sn2=so+vn1*dt*ratio;vec2 err=sn2-sn;vec2 sn3=sn-err/2.0;vec2 v2=texture2D(velocity,sn3).xy;vec2 so2=sn3-v2*dt*ratio;gl_FragColor=vec4(texture2D(velocity,so2).xy,0.0,0.0);}}';
    var col_frag='precision highp float;uniform sampler2D velocity;uniform sampler2D palette;uniform vec4 bgColor;varying vec2 uv;void main(){vec2 vel=texture2D(velocity,uv).xy;float lenv=clamp(length(vel),0.0,1.0);vec3 c=texture2D(palette,vec2(lenv,0.5)).rgb;vec3 outRGB=mix(bgColor.rgb,c,lenv);float outA=mix(bgColor.a,1.0,lenv);gl_FragColor=vec4(outRGB,outA);}';
    var div_frag='precision highp float;uniform sampler2D velocity;uniform float dt;uniform vec2 px;varying vec2 uv;void main(){float x0=texture2D(velocity,uv-vec2(px.x,0.0)).x;float x1=texture2D(velocity,uv+vec2(px.x,0.0)).x;float y0=texture2D(velocity,uv-vec2(0.0,px.y)).y;float y1=texture2D(velocity,uv+vec2(0.0,px.y)).y;gl_FragColor=vec4((x1-x0+y1-y0)/2.0/dt);}';
    var ext_frag='precision highp float;uniform vec2 force;uniform vec2 center;uniform vec2 scale;uniform vec2 px;varying vec2 vUv;void main(){vec2 c=(vUv-0.5)*2.0;float d=1.0-min(length(c),1.0);d*=d;gl_FragColor=vec4(force*d,0.0,1.0);}';
    var poi_frag='precision highp float;uniform sampler2D pressure;uniform sampler2D divergence;uniform vec2 px;varying vec2 uv;void main(){float p0=texture2D(pressure,uv+vec2(px.x*2.0,0.0)).r;float p1=texture2D(pressure,uv-vec2(px.x*2.0,0.0)).r;float p2=texture2D(pressure,uv+vec2(0.0,px.y*2.0)).r;float p3=texture2D(pressure,uv-vec2(0.0,px.y*2.0)).r;float div=texture2D(divergence,uv).r;gl_FragColor=vec4((p0+p1+p2+p3)/4.0-div);}';
    var pres_frag='precision highp float;uniform sampler2D pressure;uniform sampler2D velocity;uniform vec2 px;uniform float dt;varying vec2 uv;void main(){float p0=texture2D(pressure,uv+vec2(px.x,0.0)).r;float p1=texture2D(pressure,uv-vec2(px.x,0.0)).r;float p2=texture2D(pressure,uv+vec2(0.0,px.y)).r;float p3=texture2D(pressure,uv-vec2(0.0,px.y)).r;vec2 v=texture2D(velocity,uv).xy;v=v-vec2(p0-p1,p2-p3)*0.5*dt;gl_FragColor=vec4(v,0.0,1.0);}';
    var vis_frag='precision highp float;uniform sampler2D velocity;uniform sampler2D velocity_new;uniform float v;uniform vec2 px;uniform float dt;varying vec2 uv;void main(){vec2 old=texture2D(velocity,uv).xy;vec2 n0=texture2D(velocity_new,uv+vec2(px.x*2.0,0.0)).xy;vec2 n1=texture2D(velocity_new,uv-vec2(px.x*2.0,0.0)).xy;vec2 n2=texture2D(velocity_new,uv+vec2(0.0,px.y*2.0)).xy;vec2 n3=texture2D(velocity_new,uv-vec2(0.0,px.y*2.0)).xy;vec2 nv=4.0*old+v*dt*(n0+n1+n2+n3);nv/=4.0*(1.0+v*dt);gl_FragColor=vec4(nv,0.0,0.0);}';

    /* ── FBO helpers ── */
    var isIOS=/iPad|iPhone|iPod/i.test(navigator.userAgent);
    var fboType=isIOS?THREE.HalfFloatType:THREE.FloatType;
    function makeFBO(w,h){
        return new THREE.WebGLRenderTarget(w,h,{type:fboType,depthBuffer:false,stencilBuffer:false,
            minFilter:THREE.LinearFilter,magFilter:THREE.LinearFilter,
            wrapS:THREE.ClampToEdgeWrapping,wrapT:THREE.ClampToEdgeWrapping});
    }

    /* ── ShaderPass ── */
    function ShaderPass(props){
        this.props=props;this.scene=new THREE.Scene();this.camera=new THREE.Camera();this.plane=null;
        if(props.material){
            this.material=new THREE.RawShaderMaterial(props.material);
            this.plane=new THREE.Mesh(new THREE.PlaneGeometry(2,2),this.material);
            this.scene.add(this.plane);
        }
        this.uniforms=props.material&&props.material.uniforms;
    }
    ShaderPass.prototype.render=function(out){
        renderer.setRenderTarget(out||null);renderer.render(this.scene,this.camera);renderer.setRenderTarget(null);
    };

    /* ── Simulation setup ── */
    var fboW=Math.max(1,Math.round(resolution*W));
    var fboH=Math.max(1,Math.round(resolution*H));
    var cellScale=new THREE.Vector2(1/fboW,1/fboH);
    var fboSize=new THREE.Vector2(fboW,fboH);
    var boundary=new THREE.Vector2();

    var fbos={
        vel_0:makeFBO(fboW,fboH),vel_1:makeFBO(fboW,fboH),
        vel_v0:makeFBO(fboW,fboH),vel_v1:makeFBO(fboW,fboH),
        div:makeFBO(fboW,fboH),p0:makeFBO(fboW,fboH),p1:makeFBO(fboW,fboH)
    };

    /* Advection */
    var advPass=new ShaderPass({material:{vertexShader:face_vert,fragmentShader:adv_frag,uniforms:{
        boundarySpace:{value:cellScale},px:{value:cellScale},fboSize:{value:fboSize},
        velocity:{value:fbos.vel_0.texture},dt:{value:dt},isBFECC:{value:BFECC}
    }},output:fbos.vel_1});
    var boundaryG=new THREE.BufferGeometry();
    boundaryG.setAttribute('position',new THREE.BufferAttribute(new Float32Array([-1,-1,0,-1,1,0,-1,1,0,1,1,0,1,1,0,1,-1,0,1,-1,0,-1,-1,0]),3));
    var boundaryLine=new THREE.LineSegments(boundaryG,new THREE.RawShaderMaterial({vertexShader:line_vert,fragmentShader:adv_frag,uniforms:advPass.uniforms}));
    advPass.scene.add(boundaryLine);

    /* External Force */
    var extPass=new ShaderPass({output:fbos.vel_1});
    var mouseG=new THREE.PlaneGeometry(1,1);
    var mouseMat=new THREE.RawShaderMaterial({vertexShader:mouse_vert,fragmentShader:ext_frag,
        blending:THREE.AdditiveBlending,depthWrite:false,
        uniforms:{px:{value:cellScale},force:{value:new THREE.Vector2()},center:{value:new THREE.Vector2()},scale:{value:new THREE.Vector2(cursorSize,cursorSize)}}});
    var mouseMesh=new THREE.Mesh(mouseG,mouseMat);extPass.scene.add(mouseMesh);

    /* Viscous */
    var visPass=new ShaderPass({material:{vertexShader:face_vert,fragmentShader:vis_frag,uniforms:{
        boundarySpace:{value:boundary},velocity:{value:fbos.vel_1.texture},
        velocity_new:{value:fbos.vel_v0.texture},v:{value:viscousVal},px:{value:cellScale},dt:{value:dt}
    }},output:fbos.vel_v1,out0:fbos.vel_v0,out1:fbos.vel_v1});

    /* Divergence */
    var divPass=new ShaderPass({material:{vertexShader:face_vert,fragmentShader:div_frag,uniforms:{
        boundarySpace:{value:boundary},velocity:{value:fbos.vel_v0.texture},px:{value:cellScale},dt:{value:dt}
    }},output:fbos.div});

    /* Poisson */
    var poiPass=new ShaderPass({material:{vertexShader:face_vert,fragmentShader:poi_frag,uniforms:{
        boundarySpace:{value:boundary},pressure:{value:fbos.p0.texture},divergence:{value:fbos.div.texture},px:{value:cellScale}
    }},output:fbos.p1,out0:fbos.p0,out1:fbos.p1});

    /* Pressure */
    var presPass=new ShaderPass({material:{vertexShader:face_vert,fragmentShader:pres_frag,uniforms:{
        boundarySpace:{value:boundary},pressure:{value:fbos.p0.texture},velocity:{value:fbos.vel_v0.texture},px:{value:cellScale},dt:{value:dt}
    }},output:fbos.vel_0});

    /* Output */
    var outScene=new THREE.Scene(),outCam=new THREE.Camera();
    var outMesh=new THREE.Mesh(new THREE.PlaneGeometry(2,2),new THREE.RawShaderMaterial({
        vertexShader:face_vert,fragmentShader:col_frag,transparent:true,depthWrite:false,
        uniforms:{velocity:{value:fbos.vel_0.texture},boundarySpace:{value:new THREE.Vector2()},palette:{value:paletteTex},bgColor:{value:bgVec4}}
    }));
    outScene.add(outMesh);

    /* ── Simulate ── */
    function simulate(){
        boundary.copy(isBounce?new THREE.Vector2(0,0):cellScale);
        /* Advection */
        advPass.uniforms.dt.value=dt;advPass.uniforms.isBFECC.value=BFECC;
        boundaryLine.visible=isBounce;advPass.render(fbos.vel_1);
        /* External force */
        var fx=mDiff.x/2*mouseForce,fy=mDiff.y/2*mouseForce;
        var csx=cursorSize*cellScale.x,csy=cursorSize*cellScale.y;
        mouseMat.uniforms.force.value.set(fx,fy);
        mouseMat.uniforms.center.value.set(
            Math.min(Math.max(mCoords.x,-1+csx+cellScale.x*2),1-csx-cellScale.x*2),
            Math.min(Math.max(mCoords.y,-1+csy+cellScale.y*2),1-csy-cellScale.y*2));
        mouseMat.uniforms.scale.value.set(cursorSize,cursorSize);
        extPass.render(fbos.vel_1);
        /* Viscous */
        var vel=fbos.vel_1;
        if(isViscous){
            visPass.uniforms.v.value=viscousVal;
            for(var i=0;i<iterViscous;i++){
                var fi=i%2===0?visPass.props.out0:visPass.props.out1;
                var fo=i%2===0?visPass.props.out1:visPass.props.out0;
                visPass.uniforms.velocity_new.value=fi.texture;visPass.props.output=fo;
                visPass.uniforms.dt.value=dt;visPass.render(fo);
            }
            vel=fo;
        }
        /* Divergence */
        divPass.uniforms.velocity.value=vel.texture;divPass.render(fbos.div);
        /* Poisson */
        var pOut=fbos.p1;
        for(var j=0;j<iterPoisson;j++){
            var pi=j%2===0?poiPass.props.out0:poiPass.props.out1;
            var po=j%2===0?poiPass.props.out1:poiPass.props.out0;
            poiPass.uniforms.pressure.value=pi.texture;poiPass.props.output=po;poiPass.render(po);pOut=po;
        }
        /* Pressure */
        presPass.uniforms.velocity.value=vel.texture;presPass.uniforms.pressure.value=pOut.texture;presPass.render(fbos.vel_0);
    }

    /* ── Loop ── */
    var raf=null,running=false;
    function loop(){
        if(!running)return;
        autoUpdate();mouseUpdate();
        var d=clock.getDelta();time+=d;
        simulate();
        renderer.setRenderTarget(null);renderer.render(outScene,outCam);
        raf=requestAnimationFrame(loop);
    }
    function start(){if(running)return;running=true;loop();}
    function pause(){running=false;if(raf){cancelAnimationFrame(raf);raf=null;}}

    /* ── Resize ── */
    var ro=new ResizeObserver(function(){
        doResize();
        fboW=Math.max(1,Math.round(resolution*W));fboH=Math.max(1,Math.round(resolution*H));
        cellScale.set(1/fboW,1/fboH);fboSize.set(fboW,fboH);
        for(var k in fbos)fbos[k].setSize(fboW,fboH);
    });
    ro.observe(mount);

    /* ── Visibility ── */
    var io=new IntersectionObserver(function(entries){
        var v=entries[0].isIntersecting&&entries[0].intersectionRatio>0;
        if(v&&!document.hidden)start();else pause();
    },{threshold:[0,0.01,0.1]});
    io.observe(mount);
    document.addEventListener('visibilitychange',function(){document.hidden?pause():(running||start());});

    start();
}

function __leInitAll(){
    document.querySelectorAll('.liquid-ether-mount:not([data-le-init])').forEach(function(m){
        m.dataset.leInit='1';__leInit(m);
    });
}

/* Load Three.js once, then init all mounts */
if(!document.getElementById('__le-three')){
    var s=document.createElement('script');
    s.id='__le-three';
    s.src='https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js';
    s.onload=function(){
        if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',__leInitAll);
        else __leInitAll();
    };
    document.head.appendChild(s);
} else if(window.THREE){
    if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',__leInitAll);
    else __leInitAll();
}
})();
</script>
@endonce
