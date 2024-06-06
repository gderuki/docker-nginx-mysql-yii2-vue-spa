<template>
    <a-spin tip='Loading Task' v-if='task === null'>
    </a-spin>
    <a-card hoverable style='width: 60%' v-else>
        <template #actions>
            <button @click="showAllTasks">Show All Tasks</button>
            <a-button @click='showEditTaskForm' style='margin-right: 5px'>
                Edit
            </a-button>
            <a-popconfirm title='Delete task? This action cannot be undone' @confirm='deleteTask' okText='Delete'
                okType='danger'>
                <template #icon>
                    <WarningOutlined style='color: red' />
                </template>
                <a-button danger>
                    Delete
                </a-button>
            </a-popconfirm>
        </template>
        <a-card-meta :title='task.title' />
        <a-row style='margin-top: 50px'>
            <a-col :span='18'>
                <a-statistic :value='task.description' />
            </a-col>
        </a-row>
    </a-card>
</template>

<script lang="ts">
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api'; // replace with your actual api import
import { Task } from '@/types';

export default {
    props: {
        taskId: {
            type: String,
            required: true
        },
    },
    setup(props: any) {
        const task = ref<Task | null>(null);
        const router = useRouter();

        const loadTask = async () => {
            task.value = await api.helpGet(`tasks/${props.taskId}`);
        };

        const showAllTasks = () => {
            router.push({ name: 'tasks' });
        };

        const showEditTaskForm = () => {
            router.push({ name: 'task-form', params: { taskId: props.taskId } });
        };

        const deleteTask = async () => {
            await api.helpDelete(`tasks/${props.taskId}`);
            showAllTasks();
        };

        onMounted(async () => {
            await loadTask();
        });

        return {
            task,
            showAllTasks,
            showEditTaskForm,
            deleteTask
        };
    }
};
</script>