import Vue from "vue";
// plugins/mqtt.client.js
// npm i mqtt
import mqtt from "mqtt";

/**
 * Simple wildcard matcher for MQTT topics
 * Supports + (single level) and # (multi-level)
 */
function topicMatches(pattern, topic) {
  if (pattern === topic) return true;
  const p = pattern.split("/");
  const t = topic.split("/");

  for (let i = 0; i < p.length; i++) {
    const pp = p[i];
    const tt = t[i];

    if (pp === "#") return true; // match the rest
    if (pp === "+") continue; // match this level
    if (tt === undefined || pp !== tt) return false;
  }
  return p.length === t.length;
}

export default (ctx, inject) => {
  // Update this URL/options to match your broker
  const url = "wss://mqtt.xtremeguard.org:8084"; //process.env.MQTT_URL || "wss://mqtt.xtremeguard.org:8084";
  const opts = {
    // clientId: 'reception-' + Math.random().toString(16).slice(2),
    clean: true,
    reconnectPeriod: 2000,
    connectTimeout: 10_000,
    // username: process.env.MQTT_USER,
    // password: process.env.MQTT_PASS,
  };

  const client = mqtt.connect(url, opts);

  const $mqtt = {
    /**
     * Publish JSON or string
     */
    pub(topic, payload, options) {
      let data = payload;
      if (typeof payload !== "string") {
        try {
          data = JSON.stringify(payload);
        } catch {
          data = String(payload);
        }
      }
      client.publish(topic, data, options || {});
    },

    /**
     * Subscribe with per-subscription message filter and cleanup.
     * Returns () => { unsubscribe + removeListener }.
     */
    sub(topic, handler) {
      const onMessage = (actualTopic, buf) => {
        if (typeof actualTopic !== "string") return; // guard bad calls
        if (!topicMatches(topic, actualTopic)) return;

        let parsed = null;
        const text = buf ? buf.toString() : "";
        try {
          parsed = JSON.parse(text);
        } catch {
          parsed = text;
        }
        handler(parsed, actualTopic);
      };

      client.on("message", onMessage);
      client.subscribe(topic, (err) => {
        if (err) console.error("MQTT subscribe error:", err, "topic=", topic);
      });

      return () => {
        try {
          client.removeListener("message", onMessage);
        } catch {}
        try {
          client.unsubscribe(topic);
        } catch {}
      };
    },

    // expose raw client if needed (read-only usage preferred)
    get raw() {
      return client;
    },
  };

  inject("mqtt", $mqtt);
};
