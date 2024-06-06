<template>
    <a-button type='primary' style='margin-bottom: 8px' @click='showAddTaskForm'>
        Add Task
    </a-button>
    <a-table :dataSource='tasks' :columns='columns' rowKey='id'>
        <template #action='{ record }'>
            <div>
                <a-button type='primary' @click='showTask(record.id)' style='margin-right: 5px' ghost>
                    View
                </a-button>
                <a-button @click='showEditTaskForm(record.id)' style='margin-right: 5px'>
                    Edit
                </a-button>
                <a-popconfirm title='Delete task? This action cannot be undone' @confirm='deleteTask(record.id)'
                    okText='Delete' okType='danger'>
                    <template #icon>
                        <WarningOutlined style='color: red' />
                    </template>
                    <a-button danger>
                        Delete
                    </a-button>
                </a-popconfirm>
            </div>
        </template>
    </a-table>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from 'vue';
import api from '@/api';
import { WarningOutlined } from '@ant-design/icons-vue';
import { useRouter } from 'vue-router';
import { Task } from '@/types';

export default defineComponent({
    components: {
        WarningOutlined
    },
    setup() {
        const tasks = ref<Task[]>([]);
        const router = useRouter();

        const columns = [
            {
                title: 'Title',
                dataIndex: 'title',
                key: 'title',
                ellipsis: true
            },
            {
                title: 'Description',
                dataIndex: 'description',
                key: 'description',
                ellipsis: true
            },
            {
                title: 'Status',
                dataIndex: 'status',
                key: 'status',
                ellipsis: true
            },
            {
                title: 'Action',
                key: 'action',
                slots: { customRender: 'action' },
            },
        ] as const;

        const deleteTask = async (taskId: string) => {
            await api.helpDelete(`tasks/${taskId}`);
            tasks.value = tasks.value.filter(({ id }) => id !== taskId);
        };

        const showTask = (taskId: string) => {
            router.push({ name: 'task-item', params: { taskId } });
        };

        const showAddTaskForm = () => {
            router.push('/task/add');
        };

        const showEditTaskForm = (taskId: string) => {
            router.push({ name: 'task-form', params: { taskId } });
        };

        onMounted(async () => {
            tasks.value = await api.helpGet('tasks');
        });

        return {
            tasks,
            columns,
            deleteTask,
            showTask,
            showAddTaskForm,
            showEditTaskForm
        };
    }
});
</script>