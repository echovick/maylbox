<script setup lang="ts">
import { ref, watch, nextTick } from 'vue';

const props = defineProps<{
    html: string;
}>();

const iframeRef = ref<HTMLIFrameElement | null>(null);
let writing = false;

function writeContent() {
    if (writing) return;
    const iframe = iframeRef.value;
    if (!iframe || !props.html) return;

    const doc = iframe.contentDocument;
    if (!doc) return;

    writing = true;
    doc.open();
    doc.write(`<!DOCTYPE html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  body { margin: 0; padding: 12px 16px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 14px; color: #1a1a1a; background: #fff; overflow-x: hidden; word-wrap: break-word; }
  img { max-width: 100%; height: auto; }
  table { max-width: 100% !important; }
  pre { white-space: pre-wrap; }
</style>
</head><body>${props.html}</body></html>`);
    doc.close();
    writing = false;

    const resize = () => {
        if (doc.body) {
            iframe.style.height = doc.body.scrollHeight + 'px';
        }
    };
    doc.querySelectorAll('img').forEach(img => {
        img.addEventListener('load', resize);
    });
    setTimeout(resize, 100);
    setTimeout(resize, 500);
}

watch(() => props.html, () => nextTick(writeContent), { immediate: true });
</script>

<template>
    <iframe
        ref="iframeRef"
        class="w-full border-0 rounded-md"
        sandbox="allow-same-origin"
        style="min-height: 100px"
    />
</template>
