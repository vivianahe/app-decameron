<template>
    <div id="main-wrapper" class="mini-sidebar pb-16">
        <Loader :loading="loading" />
        <!--DataTable-->
        <DataTable class="tabla-m" :title="'Hoteles'" :showSearch="true" :headers="headers" :items="dataHotel"
            :button_add="true" @click-add="dialogVisible = true, titleModal = 'AGREGAR HOTEL'">
            <template v-slot:[`item.options`]="{ item }">
                <v-container>
                    <v-row align="center" justify="center">
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props"
                                        @click="titleModal = 'EDITAR HOTEL', dialogVisible = true, getHotelId(item.id)">
                                        <v-icon color="#a1a5b7">mdi mdi-file-document-edit</v-icon></v-btn>
                                </template>
                                <span>Editar</span>
                            </v-tooltip>
                        </v-col>
                        <v-col cols="auto">
                            <v-tooltip text="Tooltip">
                                <template v-slot:activator="{ props }">
                                    <v-btn icon size="small" v-bind="props" @click="confirmDeletion(item.id)">
                                        <v-icon color="#a1a5b7">mdi mdi-trash-can</v-icon>
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
    <!--Modal add or update-->
    <v-dialog width="1000" v-model="dialogVisible" persistent>
        <v-card :title=titleModal>
            <v-form @submit.prevent="setData">
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field :error-messages="errorMessages.name" v-model="hotel.name" label="Nombre *"
                                required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.nit" v-model="hotel.nit" label="Nit *"
                                type="text" required></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.address" v-model="hotel.address"
                                label="Dirección *" type="text" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="6">
                            <v-text-field :error-messages="errorMessages.city" v-model="hotel.city" label="Ciudad *"
                                type="text" required></v-text-field>
                        </v-col>
                        <v-col cols="6">
                            <v-text-field v-model="hotel.total_rooms" :error-messages="errorMessages.total_rooms"
                                @keypress="onlyNumbers" label="Num habitaciones *" required></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-btn color="#0f172a" type="submit" class="mr-2">GUARDAR</v-btn>
                            <v-btn color="light" @click="clearFrm()">CANCELAR</v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-form>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import DataTable from '../utilities/Datatable.vue'
import Swal from 'sweetalert2';
import Loader from "../utilities/Loader.vue";
import { useRouter } from "vue-router";
const router = useRouter()
const dataHotel = ref([]);
const titleModal = ref('AGREGAR HOTEL');
const errorMessages = ref({
    name: null,
    address: null,
    city: null,
    nit: null,
    total_rooms: null
});
const loading = ref(true);
const headers = ref([
    { title: "Nombre", align: "start", sortable: false, key: "name" },
    { title: "Nit", align: "center", sortable: false, key: "nit" },
    { title: "Dirección", align: "center", sortable: false, key: "address" },
    { title: "Ciudad", align: "center", sortable: false, key: "city" },
    { title: "N° Habitaciones", align: "center", sortable: false, key: "total_rooms" },
    { title: "Acción", align: "center", sortable: false, key: "options" },
]);

const hotel = ref({
    id: null,
    name: "",
    nit: "",
    address: "",
    city: "",
    total_rooms: ""
});

const getHotelId = (item) => {
    loading.value = true;
    axios
        .get("/getHotelId/" + item)
        .then((response) => {
            hotel.value.id = response.data.id;
            hotel.value.name = response.data.name;
            hotel.value.nit = response.data.nit;
            hotel.value.address = response.data.address;
            hotel.value.city = response.data.city;
            hotel.value.total_rooms = response.data.total_rooms;
            loading.value = false;
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        });

};

const confirmDeletion = (id) => {
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
            deleteHotel(id);
        }
    });
};
const deleteHotel = async (id) => {
    loading.value = true;
    axios
        .get("/deleteHotel/" + id)
        .then((response) => {
            if (response.data.message === 'Exists') {
                Swal.fire({
                    title: "¡Atención!",
                    text: "No se puede eliminar el hotel porque ya tiene habitaciones relacionadas.",
                    icon: "warning",
                });
                loading.value = false;
            } else {
                Swal.fire({
                    title: "¡Borrado!",
                    text: "El hotel ha sido eliminado",
                    icon: "success",
                });
                loading.value = true;
                getHotel();
            }
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        });
};
const dialogVisible = ref(false);
const validateFields = () => {
    const param = hotel.value;
    if (param.name.trim() === "") {
        errorMessages.value.name = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.value.name = "";
    }
    if (param.nit.trim() === "") {
        errorMessages.value.nit = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.value.nit = "";
    }

    if (param.address.trim() === "") {
        errorMessages.value.address = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.value.address = "";
    }
    if (param.city.trim() === "") {
        errorMessages.value.city = "Este campo es obligatorio.";
        return false;
    } else {
        errorMessages.value.city = "";
    }
    if (typeof param.total_rooms === 'string' && (param.total_rooms.trim() === "" || param.total_rooms.trim() === '""')) {
        errorMessages.value.total_rooms = "Este campo es obligatorio.";
        return false;
    }
    else {
        errorMessages.value.total_rooms = "";
    }
    return true;
};

const getHotel = async () => {
    await axios
        .get("/getHotel")
        .then((response) => {
            dataHotel.value = response.data;
            loading.value = false;
        })
        .catch((error) => {
            console.error(error);
            loading.value = false;
        })
};

const onlyNumbers = (event) => {
    let keyCode = (event.keyCode ? event.keyCode : event.which);
    // Permitir solo números del 0 al 9
    if (keyCode < 48 || keyCode > 57) {
        event.preventDefault();
    }
};

const setHotel = () => {
    if (!validateFields()) {
        return;
    }
    const formData = {
        id: hotel.value.id,
        name: hotel.value.name,
        address: hotel.value.address,
        city: hotel.value.city,
        nit: hotel.value.nit,
        total_rooms: hotel.value.total_rooms
    };
    loading.value = true;
    axios
        .post('/setHotel', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                clearFrm();
                getHotel();
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

const setData = () => {
    if (titleModal.value === 'AGREGAR HOTEL') {
        setHotel();
    } else {
        updateHotel();
    }
};

const updateHotel = () => {
    if (!validateFields()) {
        return;
    }
    const formData = {
        id: hotel.value.id,
        name: hotel.value.name,
        address: hotel.value.address,
        city: hotel.value.city,
        nit: hotel.value.nit,
        total_rooms: hotel.value.total_rooms
    };

    loading.value = true;
    axios
        .post('/updateHotel', formData)
        .then((response) => {
            if (response.data.message) {
                Swal.fire("Correcto!", response.data.message, "success");
                clearFrm();
                getHotel();
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
    hotel.value.id = "";
    hotel.value.name = "";
    hotel.value.address = "";
    hotel.value.city = "";
    hotel.value.nit = "";
    hotel.value.total_rooms = "";
    loading.value = false;
};

const getRedirection = async () => {
    const response = await axios.get(`/getInitialRedirectPath`);
    if (response.data != 'permission') {
        router.push({ path: response.data });
    }
}
onMounted(async () => {
    await getRedirection();
    await getHotel();
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
</style>
