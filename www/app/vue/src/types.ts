// types.ts
export interface Task {
    id?: string;
    title: string;
    description: string;
    status: number;
    touched: {
        title: boolean;
        description: boolean;
        status: boolean;
    };
}