importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.3.2/firebase-messaging.js');

firebase.initializeApp({
    apiKey: "AIzaSyC49BF2VUL38n-xFwb97HOu3Ua3brcjKVM",
    projectId: "notification-demo-tech",
    messagingSenderId: "700634860029",
    appId: "1:700634860029:web:096df0741df61bf50ef194"
});

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function({data:{title,body,icon}}) {
    return self.registration.showNotification(title,{body,icon});
});
