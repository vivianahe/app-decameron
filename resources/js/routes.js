import { createRouter, createWebHistory } from "vue-router";

import Index from "./components/hotels/Index.vue";
import HotelRoom from "./components/hotel_rooms/Index.vue";
import User from "./components/users/Index.vue";

const routes = [
    {
        path: "/",
        component: Index,
    },
    {
        path: "/hotel_rooms",
        name: "hotel_rooms",
        component: HotelRoom,
    },
    {
        path: "/users",
        name: "users",
        component: User,
    }
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});
export default router;
