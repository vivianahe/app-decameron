<template>
    <div id="main-wrapper" class="mini-sidebar pb-16">
        <Loader :loading="loading" />
        <DataTable ref="datatableHotelRoom" class="tabla-m" :title="'Habitaciones'" :headers="headers"
            :items="dataHotelRoom" :button_add="true" :elevation="25" :showSearch="true" @click-add="dialogVisible = true,
                titleModal = 'AGREGAR HABITACIÓN',
                hotelRoom.hotel = hotel_id, getRoomTypes()">
            <template v-slot:autocomplet-header>
                <div class="d-flex align-center ml-4">
                    <div class="col-6">
                        <v-autocomplete label="Selecciona un hotel" v-model="hotel_id" :items="dataHotel"
                            item-title="text" item-value="value" :loading="loading" loading-text="Cargando hoteles..."
                            @update:modelValue="getHotelRoomData(hotel_id)"></v-autocomplete>
                    </div>
                </div>
            </template>
            <template v-slot:[`item.options`]="{ item }">
                <v-container>
                    <v-row align="center" justify="center">
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props"
                                        @click="titleModal = 'EDITAR HABITACIÓN', dialogVisible = true, getHotelRoomId(item.id, item.hotel_id)"
                                        title="Editar">
                                        <v-icon color="#616161">mdi mdi-file-document-edit</v-icon></v-btn>
                                </template>
                                <span>Editar</span>
                            </v-tooltip>
                        </v-col>
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props"
                                        @click="confirmDeletion(item.id, item.hotel_id)" title="Eliminar">
                                        <v-icon color="#616161">mdi mdi-trash-can</v-icon>
                                    </v-btn>
                                </template>
                                <span>Eliminar</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                </v-container>
            </template>
        </Datatable>
    </div>
    <!--Modal add and edit-->
    <v-dialog width="1000" v-model="dialogVisible" persistent>
        <v-card :title=titleModal>
            <v-form @submit.prevent="setData">
                <div class="mx-4">
                    <v-row class="mt-1">
                        <v-col cols="6" class="mx-0">
                            <v-autocomplete label="Hotel *" :items="dataHotel" item-title="text" item-value="value"
                                v-model="hotelRoom.hotel" :loading="loading" :error-messages="errorMessages.hotel"
                                loading-text="Cargando hoteles..."></v-autocomplete>
                        </v-col>
                        <v-col cols="6" class="mx-0">
                            <v-autocomplete label="Tipo de habitación*" :items="dataRoom" item-title="text"
                                item-value="value" v-model="hotelRoom.room_type" :loading="loading"
                                :error-messages="errorMessages.room_type" loading-text="Cargando habitaciones..."
                                @update:modelValue="getAccommodation(hotelRoom.room_type)"></v-autocomplete>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6" class="mx-0">
                            <v-autocomplete label="Acomodación *" :items="dataAccommodation" item-title="text"
                                item-value="value" v-model="hotelRoom.accommodation" :loading="loading"
                                :error-messages="errorMessages.accommodation"
                                loading-text="Cargando acomodaciones..."></v-autocomplete>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field v-model="hotelRoom.quantity" label="Cantidad *" type="text"
                                @keypress="onlyNumbers" :error-messages="errorMessages.quantity"
                                required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12" class="mb-3">
                            <v-btn color="#0f172a" type="submit" class="mr-2">GUARDAR</v-btn>
                            <v-btn color="light" @click="clearFrm()">CANCELAR</v-btn>
                        </v-col>
                    </v-row>
                </div>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, onMounted, reactive } from "vue";
import axios from "axios";
import DataTable from '../utilities/Datatable.vue'
import Swal from 'sweetalert2';
import Loader from "../utilities/Loader.vue";
const loading = ref(true);
const dataHotelRoom = ref([]);
const hotel_id = ref("");
const dataHotel = ref([]);
const dataRoom = ref([]);
const dataAccommodation = ref([]);
const titleModal = ref('AGREGAR HABITACIÓN');
const datatableHotelRoom = ref(null);
const errorMessages = reactive({
    hotel: null,
    room_type: null,
    accommodation: null,
    quantity: null
});
const headers = ref([
    { title: "Cantidad", align: "center", sortable: false, key: "quantity" },
    { title: "Tipo Habitación", align: "start", sortable: false, key: "room_type" },
    { title: "Acomodación", align: "center", sortable: false, key: "accommodation" },
    { title: "Acción", align: "center", sortable: false, key: "options" },
]);

const hotelRoom = reactive({
    id: "",
    hotel: "",
    room_type: "",
    accommodation: "",
    quantity: ""
});

const getHotelRoomId = async (room_id) => {
    loading.value = true;
    try {
        const response = await axios.get("/getHotelRoomId/" + room_id);
        hotelRoom.id = response.data.id;
        hotelRoom.hotel = response.data.hotel_id;
        hotelRoom.room_type = response.data.room_type_id;
        hotelRoom.quantity = response.data.quantity;

        // Primero carga los tipos de habitación
        await getRoomTypes();

        if (hotelRoom.room_type) {
            await getAccommodation(hotelRoom.room_type);
            // Encuentra la acomodación en los datos cargados
            const accommodation = dataAccommodation.value.find(
                item => item.value === response.data.accommodation_id
            );
            hotelRoom.accommodation = accommodation ? accommodation.value : null;
        }
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const confirmDeletion = (id, hotel_id) => {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Sí, bórralo!",
    }).then((result) => {
        if (result.isConfirmed) {
            deleteHotelRoom(id, hotel_id);
        }
    });
};
const deleteHotelRoom = async (id, hotel_id) => {
    loading.value = true;
    axios
        .get("/deleteHotelRoom/" + id)
        .then((response) => {
            Swal.fire({
                title: "¡Borrado!",
                text: "La habitación ha sido eliminada del hotel.",
                icon: "success",
            });
            loading.value = false;
            getHotelRoomData(hotel_id);
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        });
};
const dialogVisible = ref(false);
const validateFields = () => {
    if (typeof hotelRoom.hotel === 'string' && (hotelRoom.hotel.trim() === "" || hotelRoom.hotel.trim() === '""')) {
        errorMessages.hotel = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.hotel = "";
    }
    if (typeof hotelRoom.room_type === 'string' && (hotelRoom.room_type.trim() === "" || hotelRoom.room_type.trim() === '""')) {
        errorMessages.room_type = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.room_type = "";
    }
    if (typeof hotelRoom.accommodation === 'string' && (hotelRoom.accommodation.trim() === "" || hotelRoom.accommodation.trim() === '""')) {
        errorMessages.accommodation = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.accommodation = "";
    }
    if (typeof hotelRoom.quantity === "string") {
        if (hotelRoom.quantity.trim() === "") {
            errorMessages.quantity = "Este campo es obligatorio.";
            return false;
        }
    } else if (typeof hotelRoom.quantity === "number") {
        if (isNaN(hotelRoom.quantity) || hotelRoom.quantity <= 0) {
            errorMessages.quantity = "Debe ser un número válido.";
            return false;
        }
    } else {
        errorMessages.quantity = "Tipo de dato no válido.";
        return false;
    }
    return true;

};

const onlyNumbers = (event) => {
    let keyCode = (event.keyCode ? event.keyCode : event.which);
    // Permitir solo números del 0 al 9
    if (keyCode < 48 || keyCode > 57) {
        event.preventDefault();
    }
};
const getHotelRoomData = async ($id) => {
    loading.value = true;
    await axios
        .get("/getHotelRoomData/" + $id)
        .then((response) => {
            dataHotelRoom.value = response.data.map((part, index) => ({
                ...part,
                room_type: part.room_type.type,
                accommodation: part.accommodation.accommodation,
                number: index + 1
            }));
            loading.value = false;
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        })
};
const getHotels = async () => {
    loading.value = true;
    try {
        const response = await axios.get("/getHotel");
        dataHotel.value = response.data.map(item => ({
            value: item.id,
            text: item.name
        }));
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const getRoomTypes = async () => {
    loading.value = true;
    try {
        const response = await axios.get("/getRoomType");
        dataRoom.value = response.data.map(item => ({
            value: item.id,
            text: item.type
        }));
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

const getAccommodation = async (roomTypeId) => {
    hotelRoom.accommodation = '';
    loading.value = true;
    try {
        const response = await axios.get(`/getAccommodation/${roomTypeId}`);
        dataAccommodation.value = response.data.map(item => ({
            value: item.id,
            text: item.accommodation
        }));
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};


const setData = () => {
    if (titleModal.value === 'AGREGAR HABITACIÓN') {
        setHotelRoom();
    } else {
        updateHotelRoom();
    }
};
const setHotelRoom = () => {
    if (!validateFields()) {
        return;
    }
    loading.value = true;
    const formData = {
        hotelRoom: hotelRoom
    };
    axios
        .post('/setHotelRoom', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                if (hotel_id.value !== "") {
                    getHotelRoomData(hotel_id.value)
                }
                clearFrm();
            } else {
                Swal.fire(
                    "Atención!",
                    response.data.error,
                    "warning"
                );
                loading.value = false;
            }
        })
        .catch((error) => {
            loading.value = false;
        });
};

const updateHotelRoom = () => {
    if (!validateFields()) {
        return;
    }
    const formData = {
        hotelRoom: hotelRoom
    };
    loading.value = true;
    axios
        .post('/updateHotelRoom', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                clearFrm();
                getHotelRoomData(hotel_id.value);
            } else {
                Swal.fire(
                    "Atención!",
                    response.data.error,
                    "warning"
                );
                loading.value = false;
            }
        })
        .catch((error) => {
            loading.value = false;
            console.error(error);
        });

};
const clearFrm = () => {
    dialogVisible.value = false;
    hotelRoom.id = "";
    hotelRoom.hotel = "";
    hotelRoom.accommodation = "";
    hotelRoom.room_type = "";
    hotelRoom.quantity = "";
    loading.value = false;
};

onMounted(async () => {
    await getHotels();
});

</script>
<style>
.card {
    border: none;
}

.card .card-body {
    background: #FFF;
}

.card-header {
    border: none;
}

.swal2-container {
    z-index: 2500;
}

.custom-control-label {
    font-size: 14px !important;
}

.orderrow {
    margin: 0 0 0 5px !important;
}

.same-size-btn {
    min-width: 170px !important;
}

.v-col-4,
.v-col-3,
.v-col-2,
.v-col-1 {
    width: 100%;
    padding: 5px !important;
}
</style>
