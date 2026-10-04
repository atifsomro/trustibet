(function(window) {
    "use strict";

    function makeAudio(url, volume) {
        const audio = new Audio(url);
        audio.preload = "auto";
        audio.volume = volume;
        return audio;
    }

    function playAudio(audio) {
        if (!audio) return;
        try {
            audio.pause();
            audio.currentTime = 0;
            const playPromise = audio.play();
            if (playPromise && typeof playPromise.catch === "function") {
                playPromise.catch(function() {});
            }
        } catch (e) {}
    }

    const base = (window.GAME_SOUND_BASE || "/sounds").replace(/\/$/, "");
    const ver = window.GAME_SOUND_VERSION ? ("?v=" + window.GAME_SOUND_VERSION) : "";

    const winSound = makeAudio(base + "/win-coins.wav" + ver, 0.95);
    const loseSound = makeAudio(base + "/lose.ogg" + ver, 0.8);
    const scratchSound = makeAudio(base + "/scratch-loop.mp3" + ver, 0.85);
    scratchSound.loop = true;

    // Color trading UI / round cues (Mixkit free SFX)
    const colorSelectSound = makeAudio(base + "/ui-select.mp3" + ver, 0.7);
    const colorBetSound = makeAudio(base + "/ui-confirm.mp3" + ver, 0.8);
    const countdownTickPool = [0, 1, 2].map(function() {
        return makeAudio(base + "/countdown-tick.mp3" + ver, 0.75);
    });
    let countdownTickIndex = 0;
    const colorRevealSound = makeAudio(base + "/result-reveal.mp3" + ver, 0.78);

    // Sharp mechanical peg/flapper click (trimmed Mixkit hard click)
    const wheelTickPool = [0, 1, 2, 3, 4, 5, 6, 7].map(function() {
        return makeAudio(base + "/wheel-tick.wav" + ver, 0.88);
    });
    let wheelTickIndex = 0;
    let wheelTickTimer = null;
    let wheelSpinActive = false;

    window.playGameWinSound = function() {
        playAudio(winSound);
    };

    window.playGameLoseSound = function() {
        playAudio(loseSound);
    };

    window.playGameOutcomeSound = function(won) {
        if (won) {
            window.playGameWinSound();
        } else {
            window.playGameLoseSound();
        }
    };

    window.startScratchSound = function() {
        try {
            scratchSound.loop = true;
            scratchSound.volume = 0.85;
            playAudio(scratchSound);
        } catch (e) {}
    };

    window.stopScratchSound = function() {
        try {
            scratchSound.pause();
            scratchSound.currentTime = 0;
        } catch (e) {}
    };

    window.playColorSelectSound = function() {
        playAudio(colorSelectSound);
    };

    window.playColorBetSound = function() {
        playAudio(colorBetSound);
    };

    window.playCountdownTickSound = function() {
        const tick = countdownTickPool[countdownTickIndex % countdownTickPool.length];
        countdownTickIndex += 1;
        tick.volume = 0.68 + Math.random() * 0.12;
        playAudio(tick);
    };

    window.playColorRevealSound = function() {
        playAudio(colorRevealSound);
    };

    function playWheelTick() {
        const tick = wheelTickPool[wheelTickIndex % wheelTickPool.length];
        wheelTickIndex += 1;
        // Tiny volume variation so rapid ticks don't sound robotic
        tick.volume = 0.78 + Math.random() * 0.17;
        playAudio(tick);
    }

    function scheduleWheelTicks(durationMs) {
        // Approximate CSS cubic-bezier(.17,.67,.18,1) velocity:
        // fast peg hits early, long gaps near the stop.
        const start = performance.now();
        const minGap = 38;
        const maxGap = 340;

        function bezierApprox(t) {
            // Strong ease-out; velocity falls off late
            const u = 1 - t;
            return 1 - (u * u * u * u);
        }

        function step(lastProgress) {
            if (!wheelSpinActive) return;
            const elapsed = performance.now() - start;
            if (elapsed >= durationMs) {
                playWheelTick();
                return;
            }

            playWheelTick();

            const t = Math.min(1, elapsed / durationMs);
            const p = bezierApprox(t);
            const nextT = Math.min(1, t + 0.01);
            const nextP = bezierApprox(nextT);
            const velocity = Math.max(0.02, (nextP - p) / 0.01);
            // Higher velocity => shorter gaps between peg clicks
            const gap = maxGap - (maxGap - minGap) * Math.min(1, velocity / 2.2);
            wheelTickTimer = setTimeout(function() {
                step(p);
            }, gap);
        }

        step(0);
    }

    window.startWheelSpinSound = function(durationMs) {
        try {
            window.stopWheelSpinSound();
            wheelSpinActive = true;
            const duration = Number(durationMs) > 0 ? Number(durationMs) : 6000;
            scheduleWheelTicks(duration);
        } catch (e) {}
    };

    window.stopWheelSpinSound = function() {
        wheelSpinActive = false;
        if (wheelTickTimer) {
            clearTimeout(wheelTickTimer);
            wheelTickTimer = null;
        }
        wheelTickPool.forEach(function(audio) {
            try {
                audio.pause();
                audio.currentTime = 0;
            } catch (e) {}
        });
    };
})(window);
