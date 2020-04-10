#!/usr/bin/env python2.7
# -*- coding: utf-8 -*-

from __future__ import print_function
import jieba
from jieba import analyse
import codecs
import numpy
import gensim
import numpy as np
import time
import datetime

# 防止中文编码报错
import sys

reload(sys)
sys.setdefaultencoding('utf-8')

model_path = './auto_score/doc2vec/dataset.doc2vec'
start_alpha = 0.02
infer_epoch = 1000
docvec_size = 200

# 计算余弦相似度
def simlarityCalu(vector1,vector2):
    vector1Mod=np.sqrt(vector1.dot(vector1))
    vector2Mod=np.sqrt(vector2.dot(vector2))
    if vector2Mod!=0 and vector1Mod!=0:
        simlarity=(vector1.dot(vector2))/(vector1Mod*vector2Mod)
    else:
        simlarity=0
    return simlarity

# 从txt文档中读取关键词,获取词向量
def doc2vec(file_name, model):
    doc = [w for x in codecs.open(file_name, 'r', 'utf-8').readlines() for w in jieba.cut(x.strip())]
    doc_vec_all = model.infer_vector(doc, alpha=start_alpha, steps=infer_epoch)
    return doc_vec_all


if __name__ == '__main__':
    argv = sys.argv
    startTime = datetime.datetime.now()
    model = gensim.models.Doc2Vec.load('./auto_score/doc2vec/dataset.doc2vec')
    p1 = './auto_score/answer_log/'+argv[1]
    p2 = './auto_score/answer_log/'+argv[2]
    p1_doc2vec = doc2vec(p1, model)
    p2_doc2vec = doc2vec(p2, model)
    sim = simlarityCalu(p1_doc2vec,p2_doc2vec)

    endTime = datetime.datetime.now()
    seconds = (endTime-startTime).seconds

    # log
    month = time.strftime("%Y%m", time.localtime())
    with open('./auto_score/answer_log/'+month+'.txt', 'a') as l:
        l.write('(doc2vec) Similarity between '+p1+' and '+p2+' : '+str(sim)+';              cost: '+str(seconds)+' seconds')
        l.write('\n')

    print(sim)
