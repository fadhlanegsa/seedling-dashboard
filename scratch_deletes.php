<?php

// To be appended into SeedlingEditController

    // ==========================================
    // DELETION LOGIC
    // ==========================================

    private function handleDeletion($modelName, $method, $id, $redirect) {
        $user = currentUser();
        $reason = sanitize($this->post('delete_reason'));

        if (empty($reason)) {
            $this->setFlash('error', 'Alasan hapus wajib diisi!');
            $this->redirect($redirect);
            return;
        }

        $model = $this->model($modelName);
        if ($model->$method($id, $user['id'], $reason)) {
            $this->setFlash('success', 'Data berhasil dihapus permanen.');
        } else {
            $this->setFlash('error', 'Gagal menghapus data.');
        }
        $this->redirect($redirect);
    }

    public function deleteBahanBaku($id) {
        $this->handleDeletion('BahanBaku', 'deleteTransaction', $id, 'seedling-admin');
    }

    public function deleteMediaMixing($id) {
        $this->handleDeletion('MediaMixing', 'deleteProduction', $id, 'seedling-admin');
    }

    public function deleteBagFilling($id) {
        $this->handleDeletion('BagFilling', 'deleteFilling', $id, 'seedling-admin');
    }

    public function deleteSeedSowing($id) {
        $this->handleDeletion('SeedSowing', 'deleteSowing', $id, 'seedling-admin');
    }

    public function deleteHarvesting($id) {
        $this->handleDeletion('SeedlingHarvest', 'deleteHarvest', $id, 'seedling-admin');
    }

    public function deleteWeaning($id) {
        $this->handleDeletion('SeedlingWeaning', 'deleteWeaning', $id, 'seedling-admin');
    }

    public function deleteEntres($id) {
        $this->handleDeletion('SeedlingEntres', 'deleteEntres', $id, 'seedling-admin');
    }

    public function deleteMutation($id) {
        $this->handleDeletion('SeedlingMutation', 'deleteMutation', $id, 'seedling-admin');
    }
